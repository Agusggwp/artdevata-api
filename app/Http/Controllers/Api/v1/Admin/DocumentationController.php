<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentationController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Documentation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $perPage = $request->get('per_page', 12);
        $documentations = $query->orderBy('sort_order', 'asc')->latest()->paginate($perPage);

        return $this->paginatedResponse($documentations, 'Daftar dokumentasi perusahaan berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $request->except('image');
        $data['sort_order'] = $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('documentations', 'public');
        }

        $documentation = Documentation::create($data);

        AuditLogger::log(
            action: 'create',
            module: 'Documentations',
            recordId: (string) $documentation->id,
            description: "Membuat dokumentasi baru (API): {$documentation->title}",
            newData: ['title' => $documentation->title]
        );

        return $this->successResponse($documentation, 'Dokumentasi berhasil ditambahkan.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $documentation = Documentation::find($id);

        if (!$documentation) {
            return $this->errorResponse('Dokumentasi tidak ditemukan.', 404);
        }

        return $this->successResponse($documentation, 'Detail dokumentasi berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $documentation = Documentation::find($id);

        if (!$documentation) {
            return $this->errorResponse('Dokumentasi tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $request->except('image');
        $data['sort_order'] = $request->input('sort_order', $documentation->sort_order);

        if ($request->hasFile('image')) {
            if ($documentation->image && Storage::disk('public')->exists($documentation->image)) {
                Storage::disk('public')->delete($documentation->image);
            }
            $data['image'] = $request->file('image')->store('documentations', 'public');
        }

        $documentation->update($data);

        AuditLogger::log(
            action: 'update',
            module: 'Documentations',
            recordId: (string) $documentation->id,
            description: "Memperbarui dokumentasi (API): {$documentation->title}",
            newData: ['title' => $documentation->title]
        );

        return $this->successResponse($documentation, 'Dokumentasi berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $documentation = Documentation::find($id);

        if (!$documentation) {
            return $this->errorResponse('Dokumentasi tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Documentations',
            recordId: (string) $documentation->id,
            description: "Menghapus dokumentasi (API): {$documentation->title}"
        );

        if ($documentation->image && Storage::disk('public')->exists($documentation->image)) {
            Storage::disk('public')->delete($documentation->image);
        }

        $documentation->delete();

        return $this->successResponse(null, 'Dokumentasi berhasil dihapus.');
    }
}
