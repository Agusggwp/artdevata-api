<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentationController extends Controller
{
    /**
     * Display a listing of company documentations.
     */
    public function index(Request $request)
    {
        $query = Documentation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $documentations = $query->orderBy('sort_order', 'asc')->latest()->paginate(12);

        return view('admin.documentations.index', compact('documentations'));
    }

    /**
     * Show the form for creating a new documentation item.
     */
    public function create()
    {
        return view('admin.documentations.create');
    }

    /**
     * Store a newly created documentation item in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer',
        ], [
            'title.required' => 'Judul dokumentasi wajib diisi.',
            'image.image'    => 'File harus berupa gambar.',
            'image.max'      => 'Ukuran gambar maksimal 4MB.',
        ]);

        $data = $request->except('image');
        $data['sort_order'] = $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('documentations', 'public');
        }

        Documentation::create($data);

        return redirect()->route('admin.documentations.index')
                         ->with('success', 'Dokumentasi perusahaan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified documentation item.
     */
    public function edit(Documentation $documentation)
    {
        return view('admin.documentations.edit', compact('documentation'));
    }

    /**
     * Update the specified documentation item in storage.
     */
    public function update(Request $request, Documentation $documentation)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer',
        ], [
            'title.required' => 'Judul dokumentasi wajib diisi.',
            'image.image'    => 'File harus berupa gambar.',
            'image.max'      => 'Ukuran gambar maksimal 4MB.',
        ]);

        $data = $request->except('image');
        $data['sort_order'] = $request->input('sort_order', $documentation->sort_order);

        if ($request->hasFile('image')) {
            if ($documentation->image && Storage::disk('public')->exists($documentation->image)) {
                Storage::disk('public')->delete($documentation->image);
            }
            $data['image'] = $request->file('image')->store('documentations', 'public');
        }

        $documentation->update($data);

        return redirect()->route('admin.documentations.index')
                         ->with('success', 'Dokumentasi perusahaan berhasil diperbarui.');
    }

    /**
     * Remove the specified documentation item from storage.
     */
    public function destroy(Documentation $documentation)
    {
        if ($documentation->image && Storage::disk('public')->exists($documentation->image)) {
            Storage::disk('public')->delete($documentation->image);
        }

        $documentation->delete();

        return redirect()->route('admin.documentations.index')
                         ->with('success', 'Dokumentasi perusahaan berhasil dihapus.');
    }
}
