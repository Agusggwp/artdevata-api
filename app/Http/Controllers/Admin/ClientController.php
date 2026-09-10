<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->get();
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'status'  => 'required|in:active,inactive',
            'notes'   => 'nullable|string',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('clients', 'public');
        }

        $client = Client::create($data);

        AuditLogger::log(
            action: 'create',
            module: 'Clients',
            recordId: (string) $client->id,
            description: "Menambahkan data klien: {$client->name}",
            newData: ['name' => $client->name, 'company' => $client->company]
        );

        return redirect()->route('admin.clients.index')
                         ->with('success', 'Client berhasil ditambahkan.');
    }

    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'status'  => 'required|in:active,inactive',
            'notes'   => 'nullable|string',
        ]);

        $oldData = $client->only(['name', 'email', 'company', 'status']);
        $data = $request->except('logo');

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
            newData: ['name' => $client->name, 'company' => $client->company, 'status' => $client->status]
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
            oldData: ['name' => $client->name, 'company' => $client->company]
        );

        if ($client->logo && Storage::disk('public')->exists($client->logo)) {
            Storage::disk('public')->delete($client->logo);
        }

        $client->delete();

        return redirect()->route('admin.clients.index')
                         ->with('success', 'Client berhasil dihapus.');
    }
}
