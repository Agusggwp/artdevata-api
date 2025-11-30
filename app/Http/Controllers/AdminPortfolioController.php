<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
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
            'images.*'    => 'nullable|image|max:2048',
            'image'       => 'nullable|image|max:2048',
            'link'        => 'nullable|url|max:255',
        ]);

        // Ambil field normal
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

        // Upload 1 main image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('portfolios', 'public');
        }

        // Upload multiple images (gallery)
        $gallery = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $gallery[] = $file->store('portfolios/gallery', 'public');
            }
        }
        $data['images'] = $gallery;

        Portfolio::create($data);

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
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'category' => 'nullable|string|max:255',
        'client' => 'nullable|string|max:255',
        'date' => 'nullable|string|max:255',
        'duration' => 'nullable|string|max:255',
        'challenge' => 'nullable|string',
        'solution' => 'nullable|string',
        'results' => 'nullable|string',
        'technologies' => 'nullable|string',
        'link' => 'nullable|url|max:255',
        'image' => 'nullable|image|max:2048',
        'images.*' => 'nullable|image|max:2048',
    ]);

    $data = $request->only([
        'title', 'description', 'category', 'client', 'date', 'duration',
        'challenge', 'solution', 'link'
    ]);

    // Convert results & technologies to array
    $data['results'] = $request->results ? array_map('trim', explode(',', $request->results)) : [];
    $data['technologies'] = $request->technologies ? array_map('trim', explode(',', $request->technologies)) : [];

    // Handle main image
    if ($request->hasFile('image')) {
        if ($portfolio->image) {
            Storage::disk('public')->delete($portfolio->image);
        }
        $data['image'] = $request->file('image')->store('portfolios', 'public');
    }

    // Handle gallery images
    $gallery = $portfolio->images ?? [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $gallery[] = $file->store('portfolios/gallery', 'public');
        }
    }
    $data['images'] = $gallery;

    $portfolio->update($data);

    return redirect()
        ->route('admin.portfolios.index')
        ->with('success', 'Portfolio berhasil diperbarui!');
}


    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->image) {
            Storage::disk('public')->delete($portfolio->image);
        }

        if (is_array($portfolio->images)) {
            foreach ($portfolio->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $portfolio->delete();

        return redirect()
            ->route('admin.portfolios.index')
            ->with('success', 'Portfolio berhasil dihapus!');
    }
}
