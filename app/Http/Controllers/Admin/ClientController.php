<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Client;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index(Request $request)
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

        $clients = $query->latest()->paginate(15)->withQueryString();
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
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
            description: "Menambahkan data klien: {$client->name}",
            newData: ['name' => $client->name, 'company' => $client->company_name]
        );

        return redirect()->route('admin.clients.index')
                         ->with('success', 'Client berhasil ditambahkan.');
    }

    public function show(Client $client)
    {
        $client->load(['projects.tasks', 'quotations.items', 'invoices', 'lead']);

        // Client 360 Financial calculations
        $invoices = $client->invoices;
        $totalInvoice = (float) $invoices->sum('total');
        $totalPaid = (float) $invoices->where('status', 'paid')->sum('total');
        $totalPending = (float) $invoices->whereIn('status', ['draft', 'sent'])->sum('total');
        $totalOverdue = (float) $invoices->where('status', 'overdue')->sum('total');
        $totalRevenue = $totalPaid;

        // Activity timeline from Audit Logs matching this client or related records
        $activityLogs = AuditLog::where(function ($query) use ($client) {
            $query->where('module', 'Clients')->where('record_id', (string) $client->id);
        })
        ->orWhere(function ($query) use ($client) {
            $projectIds = $client->projects->pluck('id')->map(fn($id) => (string) $id)->toArray();
            if (!empty($projectIds)) {
                $query->where('module', 'Projects')->whereIn('record_id', $projectIds);
            }
        })
        ->latest()
        ->take(15)
        ->get();

        return view('admin.clients.show', compact(
            'client',
            'totalInvoice',
            'totalPaid',
            'totalPending',
            'totalOverdue',
            'totalRevenue',
            'activityLogs'
        ));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
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
            description: "Memperbarui data klien: {$client->name}",
            oldData: $oldData,
            newData: ['name' => $client->name, 'status' => $client->status]
        );

        return redirect()->route('admin.clients.index')
                         ->with('success', 'Client berhasil diupdate.');
    }

    public function destroy(Client $client)
    {
        AuditLogger::log(
            action: 'delete',
            module: 'Clients',
            recordId: (string) $client->id,
            description: "Menghapus data klien: {$client->name}",
            oldData: ['name' => $client->name]
        );

        if ($client->logo && Storage::disk('public')->exists($client->logo)) {
            Storage::disk('public')->delete($client->logo);
        }

        $client->delete();

        return redirect()->route('admin.clients.index')
                         ->with('success', 'Client berhasil dihapus.');
    }
}
