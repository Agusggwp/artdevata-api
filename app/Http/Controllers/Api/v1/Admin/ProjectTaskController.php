<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectTaskController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request, string $projectId): JsonResponse
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->errorResponse('Proyek tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:admins,id',
            'priority'    => 'required|in:low,normal,high,urgent',
            'status'      => 'required|in:todo,in_progress,review,done',
            'due_date'    => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $validated = $validator->validated();

        if ($validated['status'] === 'done') {
            $validated['completed_at'] = now();
        }

        $task = $project->tasks()->create($validated);
        if (method_exists($project, 'updateAutoProgress')) {
            $project->updateAutoProgress();
        }

        AuditLogger::log(
            action: 'create',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Menambahkan Task (API) '{$task->title}' pada Proyek '{$project->name}'",
            newData: ['task_title' => $task->title, 'status' => $task->status]
        );

        return $this->successResponse($task->load('assignee'), 'Tugas berhasil ditambahkan.', 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $task = ProjectTask::find($id);

        if (!$task) {
            return $this->errorResponse('Tugas tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:admins,id',
            'priority'    => 'required|in:low,normal,high,urgent',
            'status'      => 'required|in:todo,in_progress,review,done',
            'due_date'    => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $validated = $validator->validated();

        if ($validated['status'] === 'done' && $task->status !== 'done') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'done') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);
        $project = $task->project;
        if ($project && method_exists($project, 'updateAutoProgress')) {
            $project->updateAutoProgress();
        }

        AuditLogger::log(
            action: 'update',
            module: 'Projects',
            recordId: (string) ($project?->id ?? $task->id),
            description: "Memperbarui Task (API) '{$task->title}'",
            newData: ['status' => $task->status]
        );

        return $this->successResponse($task->load('assignee'), 'Tugas berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $task = ProjectTask::find($id);

        if (!$task) {
            return $this->errorResponse('Tugas tidak ditemukan.', 404);
        }

        $project = $task->project;
        $title = $task->title;

        $task->delete();
        if ($project && method_exists($project, 'updateAutoProgress')) {
            $project->updateAutoProgress();
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Projects',
            recordId: (string) ($project?->id ?? $id),
            description: "Menghapus Task (API) '{$title}'"
        );

        return $this->successResponse(null, 'Tugas berhasil dihapus.');
    }
}
