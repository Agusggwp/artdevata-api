<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with('client', 'creator', 'project');

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

        $quotations = $query->latest()->paginate(15)->withQueryString();
        return view('admin.quotations.index', compact('quotations'));
    }

    public function create(Request $request)
    {
        if (!auth('admin')->user()?->hasPermission('quotations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Quotation.');
        }
        $clients = Client::where('status', 'active')->orWhere('status', 'prospect')->get();
        $services = Service::all();
        $projects = Project::all();

        // Auto generate Quotation number: QT-YYYYMM-XXX
        $prefix = 'QT-' . date('Ym') . '-';
        $latest = Quotation::where('quotation_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();
        
        $number = 1;
        if ($latest) {
            $parts = explode('-', $latest->quotation_number);
            $number = ((int) end($parts)) + 1;
        }
        $autoQuotationNumber = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);

        $selectedClientId = $request->query('client_id');

        return view('admin.quotations.create', compact('clients', 'services', 'projects', 'autoQuotationNumber', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()?->hasPermission('quotations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Quotation.');
        }
        $request->validate([
            'quotation_number' => 'required|string|max:100|unique:quotations,quotation_number',
            'client_id'        => 'required|exists:clients,id',
            'project_id'       => 'nullable|exists:projects,id',
            'issue_date'       => 'required|date',
            'valid_until'      => 'required|date|after_or_equal:issue_date',
            'status'           => 'required|in:draft,sent,viewed,accepted,rejected,expired,cancelled',
            'discount'         => 'nullable|numeric|min:0',
            'tax'              => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
            'terms'            => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.service_id'  => 'nullable|exists:services,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit'        => 'required|string|max:50',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0',
        ]);

        // Secure Backend Calculation
        $subtotal = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $qty = (int) $item['quantity'];
            $price = (float) $item['unit_price'];
            $itemDiscount = (float) ($item['discount'] ?? 0);
            $itemSubtotal = ($price * $qty) - $itemDiscount;
            if ($itemSubtotal < 0) {
                $itemSubtotal = 0;
            }

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
        $total = ($subtotal - $globalDiscount) + $tax;
        if ($total < 0) {
            $total = 0;
        }

        $quotation = Quotation::create([
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
            'terms'            => $request->terms ?? "1. Pembayaran DP 50% saat persetujuan.\n2. Pelunasan 50% setelah pekerjaan selesai.\n3. Penawaran berlaku sesuai tanggal validitas.",
            'created_by'       => auth('admin')->id(),
        ]);

        foreach ($itemsData as $itemRow) {
            $quotation->items()->create($itemRow);
        }

        AuditLogger::log(
            action: 'create',
            module: 'Quotations',
            recordId: (string) $quotation->id,
            description: "Membuat Quotation penawaran: {$quotation->quotation_number} (Total: Rp" . number_format($quotation->total, 0, ',', '.') . ")",
            newData: ['quotation_number' => $quotation->quotation_number, 'total' => $quotation->total]
        );

        return redirect()->route('admin.quotations.show', $quotation->id)
            ->with('success', 'Quotation penawaran berhasil dibuat.');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['client', 'creator', 'project', 'items.service']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        $clients = Client::where('status', 'active')->orWhere('status', 'prospect')->get();
        $services = Service::all();
        $projects = Project::all();

        return view('admin.quotations.edit', compact('quotation', 'clients', 'services', 'projects'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'quotation_number' => 'required|string|max:100|unique:quotations,quotation_number,' . $quotation->id,
            'client_id'        => 'required|exists:clients,id',
            'project_id'       => 'nullable|exists:projects,id',
            'issue_date'       => 'required|date',
            'valid_until'      => 'required|date|after_or_equal:issue_date',
            'status'           => 'required|in:draft,sent,viewed,accepted,rejected,expired,cancelled',
            'discount'         => 'nullable|numeric|min:0',
            'tax'              => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
            'terms'            => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.service_id'  => 'nullable|exists:services,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit'        => 'required|string|max:50',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $qty = (int) $item['quantity'];
            $price = (float) $item['unit_price'];
            $itemDiscount = (float) ($item['discount'] ?? 0);
            $itemSubtotal = ($price * $qty) - $itemDiscount;
            if ($itemSubtotal < 0) {
                $itemSubtotal = 0;
            }

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
        $total = ($subtotal - $globalDiscount) + $tax;
        if ($total < 0) {
            $total = 0;
        }

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
            description: "Memperbarui Quotation: {$quotation->quotation_number}",
            oldData: $oldData,
            newData: ['quotation_number' => $quotation->quotation_number, 'total' => $quotation->total]
        );

        return redirect()->route('admin.quotations.show', $quotation->id)
            ->with('success', 'Quotation berhasil diperbarui.');
    }

    public function destroy(Quotation $quotation)
    {
        if (!auth('admin')->user()?->hasPermission('quotations.delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus Quotation.');
        }
        AuditLogger::log(
            action: 'delete',
            module: 'Quotations',
            recordId: (string) $quotation->id,
            description: "Menghapus Quotation: {$quotation->quotation_number}",
            oldData: ['quotation_number' => $quotation->quotation_number]
        );

        $quotation->delete();

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation berhasil dihapus.');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['client', 'creator', 'items.service']);
        $pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation'));
        return $pdf->stream("Quotation-{$quotation->quotation_number}.pdf");
    }

    public function createProject(Quotation $quotation)
    {
        if ($quotation->project_id) {
            return redirect()->route('admin.projects.show', $quotation->project_id)
                ->with('info', 'Project untuk Quotation ini sudah ada.');
        }

        $client = $quotation->client;
        $projectName = "Project {$client->name} - " . ($quotation->items->first()?->description ?? 'Custom Service');

        // Auto Generate Project Number PRJ-YYYYMM-XXX
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
            'admin_id'       => auth('admin')->id(),
        ]);

        $quotation->update([
            'project_id' => $project->id,
            'status'     => 'accepted',
        ]);

        AuditLogger::log(
            action: 'create',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Membuat Proyek dari Quotation Accepted #{$quotation->quotation_number}",
            newData: ['project_name' => $project->name, 'budget' => $project->budget]
        );

        return redirect()->route('admin.projects.show', $project->id)
            ->with('success', 'Project berhasil dibuat dari Quotation.');
    }
}
