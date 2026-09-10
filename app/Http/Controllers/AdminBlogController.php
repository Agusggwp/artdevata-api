<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->get();
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'content'  => 'required|string',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $data = $request->only(['title', 'category', 'content']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog = Blog::create($data);

        AuditLogger::log(
            action: 'create',
            module: 'Blogs',
            recordId: (string) $blog->id,
            description: "Membuat artikel blog: {$blog->title}",
            newData: ['title' => $blog->title, 'category' => $blog->category]
        );

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog berhasil ditambahkan!');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'content'  => 'required|string',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $oldData = $blog->only(['title', 'category', 'content']);
        $data = $request->only(['title', 'category', 'content']);

        if ($request->hasFile('image')) {
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);

        AuditLogger::log(
            action: 'update',
            module: 'Blogs',
            recordId: (string) $blog->id,
            description: "Memperbarui artikel blog: {$blog->title}",
            oldData: $oldData,
            newData: $data
        );

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog berhasil diperbarui!');
    }

    public function destroy(Blog $blog)
    {
        AuditLogger::log(
            action: 'delete',
            module: 'Blogs',
            recordId: (string) $blog->id,
            description: "Menghapus artikel blog: {$blog->title}",
            oldData: ['title' => $blog->title]
        );

        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog berhasil dihapus!');
    }
}