<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request; // <-- tambah ini
use Illuminate\Support\Str;   // <-- tambah ini

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('admin')->latest()->paginate(15);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('admin.invoices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items.*.description' => 'required',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        $subtotal = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['price']);
        $total = $subtotal; // Tanpa PPN

        Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_address' => $request->client_address,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $total,
            'notes' => $request->notes,
            'items' => $request->items,
            'admin_id' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice berhasil dibuat');
    }

    public function show(Invoice $invoice)
    {
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        return view('admin.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        // Sama seperti store, copy logic jika perlu
        return redirect()->route('admin.invoices.index')->with('success', 'Invoice diperbarui');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue'
        ]);

        $invoice->status = $request->status;
        $invoice->save();

        // kembalikan badge HTML yang sudah ada di model
        return response()->json([
            'status' => 'ok',
            'badge'  => $invoice->status_badge
        ]);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return back()->with('success', 'Invoice dihapus');
    }
}