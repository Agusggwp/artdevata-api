<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $services = Service::latest()->get();
        return $this->successResponse($services, 'Daftar layanan berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'features'    => 'nullable|array',
            'features.*'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $request->except('features');
        $data['features'] = $request->features ?? [];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service = Service::create($data);

        AuditLogger::log(
            action: 'create',
            module: 'Services',
            recordId: (string) $service->id,
            description: "Membuat layanan baru (API): {$service->title}",
            newData: ['title' => $service->title]
        );

        return $this->successResponse($service, 'Layanan berhasil dibuat.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->errorResponse('Layanan tidak ditemukan.', 404);
        }

        return $this->successResponse($service, 'Detail layanan berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->errorResponse('Layanan tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'features'    => 'nullable|array',
            'features.*'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $request->except('features');
        $data['features'] = $request->features ?? [];

        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        AuditLogger::log(
            action: 'update',
            module: 'Services',
            recordId: (string) $service->id,
            description: "Memperbarui layanan (API): {$service->title}",
            newData: ['title' => $service->title]
        );

        return $this->successResponse($service, 'Layanan berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->errorResponse('Layanan tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Services',
            recordId: (string) $service->id,
            description: "Menghapus layanan (API): {$service->title}"
        );

        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return $this->successResponse(null, 'Layanan berhasil dihapus.');
    }
}
