<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Lead;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with('assignedStaff', 'client');

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

        $leads = $query->latest()->paginate(15)->withQueryString();
        $admins = Admin::where('status', 'active')->get();

        return view('admin.leads.index', compact('leads', 'admins'));
    }

    public function create()
    {
        if (!auth('admin')->user()?->hasPermission('leads.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Lead.');
        }
        $admins = Admin::where('status', 'active')->get();
        return view('admin.leads.create', compact('admins'));
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()?->hasPermission('leads.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Lead.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'source' => 'required|in:website,whatsapp,instagram,tiktok,facebook,referral,walk_in,other',
            'service_interest' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,contacted,qualified,negotiation,won,lost',
            'assigned_to' => 'nullable|exists:admins,id',
            'next_follow_up_at' => 'nullable|date',
        ]);

        $lead = Lead::create($validated);

        AuditLogger::log(
            action: 'create',
            module: 'CRM & Leads',
            recordId: (string) $lead->id,
            description: "Membuat Lead prospek baru: {$lead->name}",
            newData: ['name' => $lead->name, 'status' => $lead->status, 'source' => $lead->source]
        );

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead berhasil ditambahkan.');
    }

    public function show(Lead $lead)
    {
        $lead->load('assignedStaff', 'client');
        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        if (!auth('admin')->user()?->hasPermission('leads.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah Lead.');
        }
        $admins = Admin::where('status', 'active')->get();
        return view('admin.leads.edit', compact('lead', 'admins'));
    }

    public function update(Request $request, Lead $lead)
    {
        if (!auth('admin')->user()?->hasPermission('leads.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah Lead.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'source' => 'required|in:website,whatsapp,instagram,tiktok,facebook,referral,walk_in,other',
            'service_interest' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,contacted,qualified,negotiation,won,lost',
            'assigned_to' => 'nullable|exists:admins,id',
            'next_follow_up_at' => 'nullable|date',
        ]);

        $oldData = $lead->only(['name', 'status', 'assigned_to']);
        $lead->update($validated);

        AuditLogger::log(
            action: 'update',
            module: 'CRM & Leads',
            recordId: (string) $lead->id,
            description: "Memperbarui Lead prospek: {$lead->name}",
            oldData: $oldData,
            newData: ['name' => $lead->name, 'status' => $lead->status]
        );

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead berhasil diperbarui.');
    }

    public function destroy(Lead $lead)
    {
        if (!auth('admin')->user()?->hasPermission('leads.delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus Lead.');
        }
        AuditLogger::log(
            action: 'delete',
            module: 'CRM & Leads',
            recordId: (string) $lead->id,
            description: "Menghapus Lead prospek: {$lead->name}",
            oldData: ['name' => $lead->name]
        );

        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead berhasil dihapus.');
    }

    public function convert(Lead $lead)
    {
        if (!auth('admin')->user()?->hasPermission('leads.convert')) {
            abort(403, 'Anda tidak memiliki izin untuk mengkonversi Lead.');
        }
        if ($lead->client_id) {
            return redirect()->route('admin.clients.show', $lead->client_id)
                ->with('info', 'Lead ini sudah pernah dikonversi ke Client.');
        }

        // Cek jika ada Client dengan email yang sama untuk mencegah duplikasi
        $client = null;
        if ($lead->email) {
            $client = Client::where('email', $lead->email)->first();
        }

        if (!$client) {
            $client = Client::create([
                'lead_id' => $lead->id,
                'name' => $lead->name,
                'company_name' => $lead->company_name ?? $lead->name,
                'company' => $lead->company_name ?? $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'address' => $lead->address,
                'status' => 'prospect',
                'notes' => "Hasil konversi Lead #{$lead->id}. Service Interest: " . ($lead->service_interest ?? '-'),
            ]);
        } else {
            $client->update([
                'lead_id' => $lead->id,
            ]);
        }

        $lead->update([
            'client_id' => $client->id,
            'status' => 'won',
        ]);

        AuditLogger::log(
            action: 'convert',
            module: 'CRM & Leads',
            recordId: (string) $lead->id,
            description: "Mengonversi Lead '{$lead->name}' menjadi Client #{$client->id}",
            newData: ['client_id' => $client->id]
        );

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Lead berhasil dikonversi menjadi Client.');
    }
}
