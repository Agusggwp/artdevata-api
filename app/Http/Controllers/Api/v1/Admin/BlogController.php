<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Blog::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $perPage = $request->get('per_page', 15);
        $blogs = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($blogs, 'Daftar artikel blog berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'nullable|string|max:255',
            'author'   => 'nullable|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog = Blog::create($data);

        AuditLogger::log(
            action: 'create',
            module: 'Blogs',
            recordId: (string) $blog->id,
            description: "Membuat artikel blog baru (API): {$blog->title}",
            newData: ['title' => $blog->title]
        );

        return $this->successResponse($blog, 'Artikel blog berhasil dibuat.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return $this->errorResponse('Artikel blog tidak ditemukan.', 404);
        }

        return $this->successResponse($blog, 'Detail artikel blog berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return $this->errorResponse('Artikel blog tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'nullable|string|max:255',
            'author'   => 'nullable|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['title']);

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
            description: "Memperbarui artikel blog (API): {$blog->title}",
            newData: ['title' => $blog->title]
        );

        return $this->successResponse($blog, 'Artikel blog berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return $this->errorResponse('Artikel blog tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Blogs',
            recordId: (string) $blog->id,
            description: "Menghapus artikel blog (API): {$blog->title}"
        );

        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return $this->successResponse(null, 'Artikel blog berhasil dihapus.');
    }
}
