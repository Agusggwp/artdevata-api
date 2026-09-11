<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuotationController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Quotation::with('client:id,name,company_name,email', 'creator:id,name', 'project:id,name,project_number');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $quotations = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($quotations, 'Daftar quotation berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quotation_number'    => 'nullable|string|max:100|unique:quotations,quotation_number',
            'client_id'           => 'required|exists:clients,id',
            'project_id'          => 'nullable|exists:projects,id',
            'issue_date'          => 'required|date',
            'valid_until'         => 'required|date|after_or_equal:issue_date',
            'status'              => 'required|in:draft,sent,viewed,accepted,rejected,expired,cancelled',
            'discount'            => 'nullable|numeric|min:0',
            'tax'                 => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
            'terms'               => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.service_id'  => 'nullable|exists:services,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit'        => 'required|string|max:50',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $quotationNumber = $request->quotation_number;
        if (!$quotationNumber) {
            $prefix = 'QT-' . date('Ym') . '-';
            $latest = Quotation::where('quotation_number', 'like', "{$prefix}%")->orderBy('id', 'desc')->first();
            $number = 1;
            if ($latest) {
                $parts = explode('-', $latest->quotation_number);
                $number = ((int) end($parts)) + 1;
            }
            $quotationNumber = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        }

        $subtotal = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $qty = (int) $item['quantity'];
            $price = (float) $item['unit_price'];
            $itemDiscount = (float) ($item['discount'] ?? 0);
            $itemSubtotal = max(0, ($price * $qty) - $itemDiscount);

            $subtotal += $itemSubtotal;

            $itemsData[] = [
                'service_id'  => $item['service_id'] ?? null,
                'description' => $item['description'],
                'quantity'    => $qty,
                'unit'        => $item['unit'] ?? 'unit',
                'unit_price'  => $price,
                'discount'    => $itemDiscount,
                'subtotal'    => $itemSubtotal,
            ];
        }

        $globalDiscount = (float) ($request->discount ?? 0);
        $tax = (float) ($request->tax ?? 0);
        $total = max(0, ($subtotal - $globalDiscount) + $tax);

        $quotation = Quotation::create([
            'quotation_number' => $quotationNumber,
            'client_id'        => $request->client_id,
            'project_id'       => $request->project_id,
            'issue_date'       => $request->issue_date,
            'valid_until'      => $request->valid_until,
            'status'           => $request->status,
            'subtotal'         => $subtotal,
            'discount'         => $globalDiscount,
            'tax'              => $tax,
            'total'            => $total,
            'notes'            => $request->notes,
            'terms'            => $request->terms ?? "1. Pembayaran DP 50% saat persetujuan.\n2. Pelunasan 50% setelah pekerjaan selesai.",
            'created_by'       => $request->user()?->id,
        ]);

        foreach ($itemsData as $itemRow) {
            $quotation->items()->create($itemRow);
        }

        AuditLogger::log(
            action: 'create',
            module: 'Quotations',
            recordId: (string) $quotation->id,
            description: "Membuat Quotation penawaran (API): {$quotation->quotation_number}",
            newData: ['quotation_number' => $quotation->quotation_number, 'total' => $quotation->total]
        );

        return $this->successResponse($quotation->load(['client', 'creator', 'items.service']), 'Quotation berhasil dibuat.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $quotation = Quotation::with(['client', 'creator', 'project', 'items.service'])->find($id);

        if (!$quotation) {
            return $this->errorResponse('Quotation tidak ditemukan.', 404);
        }

        return $this->successResponse($quotation, 'Detail quotation berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $quotation = Quotation::find($id);

        if (!$quotation) {
            return $this->errorResponse('Quotation tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'quotation_number'    => 'required|string|max:100|unique:quotations,quotation_number,' . $quotation->id,
            'client_id'           => 'required|exists:clients,id',
            'project_id'          => 'nullable|exists:projects,id',
            'issue_date'          => 'required|date',
            'valid_until'         => 'required|date|after_or_equal:issue_date',
            'status'              => 'required|in:draft,sent,viewed,accepted,rejected,expired,cancelled',
            'discount'            => 'nullable|numeric|min:0',
            'tax'                 => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
            'terms'               => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.service_id'  => 'nullable|exists:services,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit'        => 'required|string|max:50',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $subtotal = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $qty = (int) $item['quantity'];
            $price = (float) $item['unit_price'];
            $itemDiscount = (float) ($item['discount'] ?? 0);
            $itemSubtotal = max(0, ($price * $qty) - $itemDiscount);

            $subtotal += $itemSubtotal;

            $itemsData[] = [
                'service_id'  => $item['service_id'] ?? null,
                'description' => $item['description'],
                'quantity'    => $qty,
                'unit'        => $item['unit'] ?? 'unit',
                'unit_price'  => $price,
                'discount'    => $itemDiscount,
                'subtotal'    => $itemSubtotal,
            ];
        }

        $globalDiscount = (float) ($request->discount ?? 0);
        $tax = (float) ($request->tax ?? 0);
        $total = max(0, ($subtotal - $globalDiscount) + $tax);

        $oldData = $quotation->only(['quotation_number', 'status', 'total']);

        $quotation->update([
            'quotation_number' => $request->quotation_number,
            'client_id'        => $request->client_id,
            'project_id'       => $request->project_id,
            'issue_date'       => $request->issue_date,
            'valid_until'      => $request->valid_until,
            'status'           => $request->status,
            'subtotal'         => $subtotal,
            'discount'         => $globalDiscount,
            'tax'              => $tax,
            'total'            => $total,
            'notes'            => $request->notes,
            'terms'            => $request->terms,
        ]);

        $quotation->items()->delete();
        foreach ($itemsData as $itemRow) {
            $quotation->items()->create($itemRow);
        }

        AuditLogger::log(
            action: 'update',
            module: 'Quotations',
            recordId: (string) $quotation->id,
            description: "Memperbarui Quotation (API): {$quotation->quotation_number}",
            oldData: $oldData,
            newData: ['quotation_number' => $quotation->quotation_number, 'total' => $quotation->total]
        );

        return $this->successResponse($quotation->load(['client', 'creator', 'items.service']), 'Quotation berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $quotation = Quotation::find($id);

        if (!$quotation) {
            return $this->errorResponse('Quotation tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Quotations',
            recordId: (string) $quotation->id,
            description: "Menghapus Quotation (API): {$quotation->quotation_number}",
            oldData: ['quotation_number' => $quotation->quotation_number]
        );

        $quotation->delete();

        return $this->successResponse(null, 'Quotation berhasil dihapus.');
    }

    public function createProject(string $id, Request $request): JsonResponse
    {
        $quotation = Quotation::with('client', 'items')->find($id);

        if (!$quotation) {
            return $this->errorResponse('Quotation tidak ditemukan.', 404);
        }

        if ($quotation->project_id) {
            return $this->errorResponse('Project untuk Quotation ini sudah ada.', 422, [
                'project_id' => $quotation->project_id
            ]);
        }

        $client = $quotation->client;
        $projectName = "Project {$client->name} - " . ($quotation->items->first()?->description ?? 'Custom Service');

        $prefix = 'PRJ-' . date('Ym') . '-';
        $latest = Project::where('project_number', 'like', "{$prefix}%")->orderBy('id', 'desc')->first();
        $number = 1;
        if ($latest && $latest->project_number) {
            $parts = explode('-', $latest->project_number);
            $number = ((int) end($parts)) + 1;
        }
        $projectNumber = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);

        $project = Project::create([
            'project_number' => $projectNumber,
            'client_id'      => $quotation->client_id,
            'quotation_id'   => $quotation->id,
            'name'           => $projectName,
            'description'    => "Project otomatis dibuat dari Quotation #{$quotation->quotation_number}.\n\nCatatan: " . ($quotation->notes ?? '-'),
            'status'         => 'planning',
            'priority'       => 'normal',
            'start_date'     => now()->toDateString(),
            'deadline'       => $quotation->valid_until ? $quotation->valid_until->toDateString() : now()->addDays(30)->toDateString(),
            'client'         => $client->company_name ?? $client->name,
            'budget'         => $quotation->total,
            'progress'       => 0,
            'admin_id'       => $request->user()?->id,
        ]);

        $quotation->update([
            'project_id' => $project->id,
            'status'     => 'accepted',
        ]);

        AuditLogger::log(
            action: 'create',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Membuat Proyek dari Quotation Accepted (API) #{$quotation->quotation_number}",
            newData: ['project_name' => $project->name, 'budget' => $project->budget]
        );

        return $this->successResponse($project->load('client', 'quotation'), 'Project berhasil dibuat dari Quotation.');
    }
}
