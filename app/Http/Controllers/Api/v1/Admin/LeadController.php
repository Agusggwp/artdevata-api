<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Lead::with('assignedStaff:id,name,email', 'client:id,name,company_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $perPage = $request->get('per_page', 15);
        $leads = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($leads, 'Daftar lead berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'company_name'      => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:50',
            'address'           => 'nullable|string',
            'source'            => 'required|in:website,whatsapp,instagram,tiktok,facebook,referral,walk_in,other',
            'service_interest'  => 'nullable|string|max:255',
            'estimated_budget'  => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
            'status'            => 'required|in:new,contacted,qualified,negotiation,won,lost',
            'assigned_to'       => 'nullable|exists:admins,id',
            'next_follow_up_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $lead = Lead::create($validator->validated());

        AuditLogger::log(
            action: 'create',
            module: 'CRM & Leads',
            recordId: (string) $lead->id,
            description: "Membuat Lead prospek baru (API): {$lead->name}",
            newData: ['name' => $lead->name, 'status' => $lead->status, 'source' => $lead->source]
        );

        return $this->successResponse($lead->load('assignedStaff', 'client'), 'Lead berhasil ditambahkan.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $lead = Lead::with('assignedStaff', 'client')->find($id);

        if (!$lead) {
            return $this->errorResponse('Lead tidak ditemukan.', 404);
        }

        return $this->successResponse($lead, 'Detail lead berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return $this->errorResponse('Lead tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'company_name'      => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:50',
            'address'           => 'nullable|string',
            'source'            => 'required|in:website,whatsapp,instagram,tiktok,facebook,referral,walk_in,other',
            'service_interest'  => 'nullable|string|max:255',
            'estimated_budget'  => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
            'status'            => 'required|in:new,contacted,qualified,negotiation,won,lost',
            'assigned_to'       => 'nullable|exists:admins,id',
            'next_follow_up_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $oldData = $lead->only(['name', 'status', 'assigned_to']);
        $lead->update($validator->validated());

        AuditLogger::log(
            action: 'update',
            module: 'CRM & Leads',
            recordId: (string) $lead->id,
            description: "Memperbarui Lead prospek (API): {$lead->name}",
            oldData: $oldData,
            newData: ['name' => $lead->name, 'status' => $lead->status]
        );

        return $this->successResponse($lead->load('assignedStaff', 'client'), 'Lead berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return $this->errorResponse('Lead tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'CRM & Leads',
            recordId: (string) $lead->id,
            description: "Menghapus Lead prospek (API): {$lead->name}",
            oldData: ['name' => $lead->name]
        );

        $lead->delete();

        return $this->successResponse(null, 'Lead berhasil dihapus.');
    }

    public function convert(string $id): JsonResponse
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return $this->errorResponse('Lead tidak ditemukan.', 404);
        }

        if ($lead->client_id) {
            return $this->errorResponse('Lead ini sudah pernah dikonversi ke Client.', 422, [
                'client_id' => $lead->client_id
            ]);
        }

        $client = null;
        if ($lead->email) {
            $client = Client::where('email', $lead->email)->first();
        }

        if (!$client) {
            $client = Client::create([
                'lead_id'      => $lead->id,
                'name'         => $lead->name,
                'company_name' => $lead->company_name ?? $lead->name,
                'company'      => $lead->company_name ?? $lead->name,
                'email'        => $lead->email,
                'phone'        => $lead->phone,
                'address'      => $lead->address,
                'status'       => 'prospect',
                'notes'        => "Hasil konversi Lead #{$lead->id}. Service Interest: " . ($lead->service_interest ?? '-'),
            ]);
        } else {
            $client->update(['lead_id' => $lead->id]);
        }

        $lead->update([
            'client_id' => $client->id,
            'status'    => 'won',
        ]);

        AuditLogger::log(
            action: 'convert',
            module: 'CRM & Leads',
            recordId: (string) $lead->id,
            description: "Mengonversi Lead '{$lead->name}' menjadi Client #{$client->id} (API)",
            newData: ['client_id' => $client->id]
        );

        return $this->successResponse([
            'lead'   => $lead->fresh('assignedStaff', 'client'),
            'client' => $client
        ], 'Lead berhasil dikonversi menjadi Client.');
    }
}
