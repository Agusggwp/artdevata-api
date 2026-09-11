<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Portfolio::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $portfolios = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($portfolios, 'Daftar portofolio berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'client_name'  => 'nullable|string|max:255',
            'category'     => 'nullable|string|max:255',
            'project_date' => 'nullable|date',
            'link'         => 'nullable|url|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('portfolios', 'public');
        }

        $portfolio = Portfolio::create($data);

        AuditLogger::log(
            action: 'create',
            module: 'Portfolios',
            recordId: (string) $portfolio->id,
            description: "Membuat portofolio baru (API): {$portfolio->title}",
            newData: ['title' => $portfolio->title]
        );

        return $this->successResponse($portfolio, 'Portofolio berhasil dibuat.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return $this->errorResponse('Portofolio tidak ditemukan.', 404);
        }

        return $this->successResponse($portfolio, 'Detail portofolio berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return $this->errorResponse('Portofolio tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'client_name'  => 'nullable|string|max:255',
            'category'     => 'nullable|string|max:255',
            'project_date' => 'nullable|date',
            'link'         => 'nullable|url|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            if ($portfolio->image && Storage::disk('public')->exists($portfolio->image)) {
                Storage::disk('public')->delete($portfolio->image);
            }
            $data['image'] = $request->file('image')->store('portfolios', 'public');
        }

        $portfolio->update($data);

        AuditLogger::log(
            action: 'update',
            module: 'Portfolios',
            recordId: (string) $portfolio->id,
            description: "Memperbarui portofolio (API): {$portfolio->title}",
            newData: ['title' => $portfolio->title]
        );

        return $this->successResponse($portfolio, 'Portofolio berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return $this->errorResponse('Portofolio tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Portfolios',
            recordId: (string) $portfolio->id,
            description: "Menghapus portofolio (API): {$portfolio->title}"
        );

        if ($portfolio->image && Storage::disk('public')->exists($portfolio->image)) {
            Storage::disk('public')->delete($portfolio->image);
        }

        $portfolio->delete();

        return $this->successResponse(null, 'Portofolio berhasil dihapus.');
    }
}
