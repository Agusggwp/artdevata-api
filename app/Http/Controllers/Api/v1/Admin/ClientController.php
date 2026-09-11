<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Client;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Client::withCount(['projects', 'quotations', 'invoices']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $clients = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($clients, 'Daftar klien berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'company'      => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'website'      => 'nullable|string|max:255',
            'tax_id'       => 'nullable|string|max:100',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'status'       => 'required|in:active,inactive,prospect,archived',
            'notes'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $request->except('logo');
        if (empty($data['company_name']) && !empty($data['company'])) {
            $data['company_name'] = $data['company'];
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('clients', 'public');
        }

        $client = Client::create($data);

        AuditLogger::log(
            action: 'create',
            module: 'Clients',
            recordId: (string) $client->id,
            description: "Menambahkan data klien (API): {$client->name}",
            newData: ['name' => $client->name, 'company' => $client->company_name]
        );

        return $this->successResponse($client, 'Klien berhasil ditambahkan.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $client = Client::with(['projects.tasks', 'quotations.items', 'invoices', 'lead'])->find($id);

        if (!$client) {
            return $this->errorResponse('Klien tidak ditemukan.', 404);
        }

        $invoices = $client->invoices;
        $totalInvoice = (float) $invoices->sum('total');
        $totalPaid = (float) $invoices->where('status', 'paid')->sum('total');
        $totalPending = (float) $invoices->whereIn('status', ['draft', 'sent'])->sum('total');
        $totalOverdue = (float) $invoices->where('status', 'overdue')->sum('total');

        $projectIds = $client->projects->pluck('id')->map(fn($pid) => (string) $pid)->toArray();

        $activityLogs = AuditLog::where(function ($query) use ($client, $projectIds) {
            $query->where('module', 'Clients')->where('record_id', (string) $client->id);
            if (!empty($projectIds)) {
                $query->orWhere(function ($q) use ($projectIds) {
                    $q->where('module', 'Projects')->whereIn('record_id', $projectIds);
                });
            }
        })
        ->latest()
        ->take(15)
        ->get();

        return $this->successResponse([
            'client'           => $client,
            'financial_360'    => [
                'total_invoice' => $totalInvoice,
                'total_paid'    => $totalPaid,
                'total_pending' => $totalPending,
                'total_overdue' => $totalOverdue,
                'total_revenue' => $totalPaid,
            ],
            'activity_logs'    => $activityLogs
        ], 'Detail klien berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->errorResponse('Klien tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'company'      => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'website'      => 'nullable|string|max:255',
            'tax_id'       => 'nullable|string|max:100',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'status'       => 'required|in:active,inactive,prospect,archived',
            'notes'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $oldData = $client->only(['name', 'email', 'company_name', 'status']);
        $data = $request->except('logo');
        if (empty($data['company_name']) && !empty($data['company'])) {
            $data['company_name'] = $data['company'];
        }

        if ($request->hasFile('logo')) {
            if ($client->logo && Storage::disk('public')->exists($client->logo)) {
                Storage::disk('public')->delete($client->logo);
            }
            $data['logo'] = $request->file('logo')->store('clients', 'public');
        }

        $client->update($data);

        AuditLogger::log(
            action: 'update',
            module: 'Clients',
            recordId: (string) $client->id,
            description: "Memperbarui data klien (API): {$client->name}",
            oldData: $oldData,
            newData: ['name' => $client->name, 'status' => $client->status]
        );

        return $this->successResponse($client, 'Data klien berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->errorResponse('Klien tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Clients',
            recordId: (string) $client->id,
            description: "Menghapus data klien (API): {$client->name}",
            oldData: ['name' => $client->name]
        );

        if ($client->logo && Storage::disk('public')->exists($client->logo)) {
            Storage::disk('public')->delete($client->logo);
        }

        $client->delete();

        return $this->successResponse(null, 'Klien berhasil dihapus.');
    }
}
