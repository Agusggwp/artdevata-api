<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::latest()->get();
        return view('admin.portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        return view('admin.portfolios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category'    => 'nullable|string|max:255',
            'client'      => 'nullable|string|max:255',
            'date'        => 'nullable|string|max:255',
            'duration'    => 'nullable|string|max:255',
            'challenge'   => 'nullable|string',
            'solution'    => 'nullable|string',
            'results'     => 'nullable|array',
            'technologies'=> 'nullable|array',
            'images.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'link'        => 'nullable|url|max:255',
        ]);

        $data = $request->only([
            'title',
            'description',
            'category',
            'client',
            'date',
            'duration',
            'challenge',
            'solution',
            'results',
            'technologies',
            'link'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('portfolios', 'public');
        }

        $gallery = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $gallery[] = $file->store('portfolios/gallery', 'public');
            }
        }
        $data['images'] = $gallery;

        $portfolio = Portfolio::create($data);

        AuditLogger::log(
            action: 'create',
            module: 'Portfolios',
            recordId: (string) $portfolio->id,
            description: "Membuat portofolio: {$portfolio->title}",
            newData: ['title' => $portfolio->title, 'category' => $portfolio->category, 'client' => $portfolio->client]
        );

        return redirect()
            ->route('admin.portfolios.index')
            ->with('success', 'Portfolio berhasil ditambahkan!');
    }

    public function edit(Portfolio $portfolio)
    {
        return view('admin.portfolios.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category'    => 'nullable|string|max:255',
            'client'      => 'nullable|string|max:255',
            'date'        => 'nullable|string|max:255',
            'duration'    => 'nullable|string|max:255',
            'challenge'   => 'nullable|string',
            'solution'    => 'nullable|string',
            'results'     => 'nullable|string',
            'technologies'=> 'nullable|string',
            'link'        => 'nullable|url|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'images.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
        ]);

        $oldData = $portfolio->only(['title', 'description', 'category', 'client']);
        $data = $request->only([
            'title', 'description', 'category', 'client', 'date', 'duration',
            'challenge', 'solution', 'link'
        ]);

        $data['results'] = $request->results ? array_map('trim', explode(',', $request->results)) : [];
        $data['technologies'] = $request->technologies ? array_map('trim', explode(',', $request->technologies)) : [];

        if ($request->hasFile('image')) {
            if ($portfolio->image && Storage::disk('public')->exists($portfolio->image)) {
                Storage::disk('public')->delete($portfolio->image);
            }
            $data['image'] = $request->file('image')->store('portfolios', 'public');
        }

        $gallery = $portfolio->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $gallery[] = $file->store('portfolios/gallery', 'public');
            }
        }
        $data['images'] = $gallery;

        $portfolio->update($data);

        AuditLogger::log(
            action: 'update',
            module: 'Portfolios',
            recordId: (string) $portfolio->id,
            description: "Memperbarui portofolio: {$portfolio->title}",
            oldData: $oldData,
            newData: ['title' => $portfolio->title, 'category' => $portfolio->category]
        );

        return redirect()
            ->route('admin.portfolios.index')
            ->with('success', 'Portfolio berhasil diperbarui!');
    }

    public function destroy(Portfolio $portfolio)
    {
        AuditLogger::log(
            action: 'delete',
            module: 'Portfolios',
            recordId: (string) $portfolio->id,
            description: "Menghapus portofolio: {$portfolio->title}",
            oldData: ['title' => $portfolio->title]
        );

        if ($portfolio->image && Storage::disk('public')->exists($portfolio->image)) {
            Storage::disk('public')->delete($portfolio->image);
        }

        if (is_array($portfolio->images)) {
            foreach ($portfolio->images as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
        }

        $portfolio->delete();

        return redirect()
            ->route('admin.portfolios.index')
            ->with('success', 'Portfolio berhasil dihapus!');
    }
}
