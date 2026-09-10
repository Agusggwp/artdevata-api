<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectDocumentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|in:contract,quotation,invoice,design,documentation,deliverables,other',
            'document' => [
                'required',
                'file',
                'max:20480', // 20 MB max
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,png,jpg,jpeg,webp,svg,txt',
            ],
        ], [
            'document.mimes' => 'Format file tidak diizinkan. Hanya dokumen dan media resmi yang boleh diunggah.',
        ]);

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
            'uploaded_by' => auth('admin')->id(),
        ]);

        AuditLogger::log(
            action: 'create',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Mengunggah Dokumen '{$doc->title}' ({$doc->category}) pada Proyek '{$project->name}'",
            newData: ['file_name' => $fileName]
        );

        return redirect()->back()->with('success', 'Dokumen proyek berhasil diunggah.');
    }

    public function download(ProjectDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(ProjectDocument $document)
    {
        $project = $document->project;
        $title = $document->title;

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        AuditLogger::log(
            action: 'delete',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Menghapus Dokumen '{$title}' dari Proyek '{$project->name}'"
        );

        return redirect()->back()->with('success', 'Dokumen proyek berhasil dihapus.');
    }
}
