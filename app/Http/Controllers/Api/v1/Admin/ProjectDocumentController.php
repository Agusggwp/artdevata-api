<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectDocumentController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request, string $projectId): JsonResponse
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->errorResponse('Proyek tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'title'    => 'required|string|max:255',
            'category' => 'required|in:contract,quotation,invoice,design,documentation,deliverables,other',
            'document' => [
                'required',
                'file',
                'max:20480', // 20 MB
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,png,jpg,jpeg,webp,svg,txt',
            ],
        ], [
            'document.mimes' => 'Format file tidak diizinkan. Hanya dokumen dan media resmi yang boleh diunggah.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $file = $request->file('document');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getClientMimeType();

        $filePath = $file->store('project_documents/' . $project->id, 'public');

        $doc = $project->documents()->create([
            'title'       => $request->title,
            'category'    => $request->category,
            'file_path'   => $filePath,
            'file_name'   => $fileName,
            'file_size'   => $fileSize,
            'mime_type'   => $mimeType,
            'uploaded_by' => $request->user()?->id,
        ]);

        AuditLogger::log(
            action: 'create',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Mengunggah Dokumen (API) '{$doc->title}' ({$doc->category}) pada Proyek '{$project->name}'",
            newData: ['file_name' => $fileName]
        );

        $docData = $doc->load('uploader');
        $docData->url = asset('storage/' . $doc->file_path);

        return $this->successResponse($docData, 'Dokumen proyek berhasil diunggah.', 201);
    }

    public function destroy(string $id): JsonResponse
    {
        $document = ProjectDocument::find($id);

        if (!$document) {
            return $this->errorResponse('Dokumen tidak ditemukan.', 404);
        }

        $project = $document->project;
        $title = $document->title;

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        AuditLogger::log(
            action: 'delete',
            module: 'Projects',
            recordId: (string) ($project?->id ?? $id),
            description: "Menghapus Dokumen (API) '{$title}'"
        );

        return $this->successResponse(null, 'Dokumen proyek berhasil dihapus.');
    }
}
