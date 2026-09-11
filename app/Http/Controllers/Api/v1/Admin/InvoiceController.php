<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with('admin:id,name', 'client:id,name,company_name', 'project:id,name,project_number');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('client_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $invoices = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($invoices, 'Daftar invoice berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_name'           => 'required|string|max:255',
            'client_email'          => 'nullable|email|max:255',
            'client_address'        => 'nullable|string',
            'client_id'             => 'nullable|exists:clients,id',
            'project_id'            => 'nullable|exists:projects,id',
            'quotation_id'          => 'nullable|exists:quotations,id',
            'invoice_date'          => 'required|date',
            'due_date'              => 'required|date|after_or_equal:invoice_date',
            'notes'                 => 'nullable|string',
            'terms'                 => 'nullable|string',
            'status'                => 'nullable|in:draft,sent,paid,overdue,cancelled',
            'items'                 => 'required|array|min:1',
            'items.*.description'   => 'required|string|max:255',
            'items.*.quantity'      => 'required|numeric|min:1',
            'items.*.price'         => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $items = $request->items;
        $subtotal = collect($items)->sum(fn($i) => ((float) $i['quantity']) * ((float) $i['price']));
        $tax = (float) ($request->tax ?? 0);
        $discount = (float) ($request->discount ?? 0);
        $total = max(0, ($subtotal - $discount) + $tax);

        $invoiceNumber = $request->invoice_number ?? ('INV-' . date('Ym') . '-' . strtoupper(Str::random(4)));

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'client_name'    => $request->client_name,
            'client_email'   => $request->client_email,
            'client_address' => $request->client_address,
            'client_id'      => $request->client_id,
            'project_id'     => $request->project_id,
            'quotation_id'   => $request->quotation_id,
            'invoice_date'   => $request->invoice_date,
            'due_date'       => $request->due_date,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'total'          => $total,
            'status'         => $request->status ?? 'draft',
            'notes'          => $request->notes,
            'terms'          => $request->terms,
            'items'          => $items,
            'admin_id'       => $request->user()?->id,
        ]);

        AuditLogger::log(
            action: 'create',
            module: 'Invoices',
            recordId: (string) $invoice->id,
            description: "Membuat Invoice baru (API): {$invoice->invoice_number}",
            newData: ['invoice_number' => $invoice->invoice_number, 'total' => $invoice->total]
        );

        return $this->successResponse($invoice->load('admin', 'client', 'project'), 'Invoice berhasil dibuat.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $invoice = Invoice::with('admin:id,name', 'client', 'project', 'quotation')->find($id);

        if (!$invoice) {
            return $this->errorResponse('Invoice tidak ditemukan.', 404);
        }

        return $this->successResponse($invoice, 'Detail invoice berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->errorResponse('Invoice tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'client_name'           => 'required|string|max:255',
            'client_email'          => 'nullable|email|max:255',
            'client_address'        => 'nullable|string',
            'client_id'             => 'nullable|exists:clients,id',
            'project_id'            => 'nullable|exists:projects,id',
            'invoice_date'          => 'required|date',
            'due_date'              => 'required|date|after_or_equal:invoice_date',
            'notes'                 => 'nullable|string',
            'status'                => 'required|in:draft,sent,paid,overdue,cancelled',
            'items'                 => 'required|array|min:1',
            'items.*.description'   => 'required|string|max:255',
            'items.*.quantity'      => 'required|numeric|min:1',
            'items.*.price'         => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $items = $request->items;
        $subtotal = collect($items)->sum(fn($i) => ((float) $i['quantity']) * ((float) $i['price']));
        $tax = (float) ($request->tax ?? $invoice->tax);
        $discount = (float) ($request->discount ?? $invoice->discount);
        $total = max(0, ($subtotal - $discount) + $tax);

        $oldData = $invoice->only(['invoice_number', 'status', 'total']);

        $invoice->update([
            'client_name'    => $request->client_name,
            'client_email'   => $request->client_email,
            'client_address' => $request->client_address,
            'client_id'      => $request->client_id,
            'project_id'     => $request->project_id,
            'invoice_date'   => $request->invoice_date,
            'due_date'       => $request->due_date,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'total'          => $total,
            'status'         => $request->status,
            'notes'          => $request->notes,
            'items'          => $items,
        ]);

        AuditLogger::log(
            action: 'update',
            module: 'Invoices',
            recordId: (string) $invoice->id,
            description: "Memperbarui Invoice (API): {$invoice->invoice_number}",
            oldData: $oldData,
            newData: ['invoice_number' => $invoice->invoice_number, 'total' => $invoice->total]
        );

        return $this->successResponse($invoice->load('admin', 'client', 'project'), 'Invoice berhasil diperbarui.');
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->errorResponse('Invoice tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,sent,paid,overdue,cancelled'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $oldStatus = $invoice->status;
        $invoice->status = $request->status;
        if ($request->status === 'paid' && !$invoice->paid_at) {
            $invoice->paid_at = now();
        }
        $invoice->save();

        AuditLogger::log(
            action: 'update',
            module: 'Invoices',
            recordId: (string) $invoice->id,
            description: "Mengubah status Invoice (API) '{$invoice->invoice_number}' dari {$oldStatus} ke {$invoice->status}",
            oldData: ['status' => $oldStatus],
            newData: ['status' => $invoice->status]
        );

        return $this->successResponse($invoice, 'Status invoice berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->errorResponse('Invoice tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Invoices',
            recordId: (string) $invoice->id,
            description: "Menghapus Invoice (API): {$invoice->invoice_number}",
            oldData: ['invoice_number' => $invoice->invoice_number]
        );

        $invoice->delete();

        return $this->successResponse(null, 'Invoice berhasil dihapus.');
    }
}
