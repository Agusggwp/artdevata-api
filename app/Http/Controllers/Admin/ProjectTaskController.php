<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:admins,id',
            'priority'    => 'required|in:low,normal,high,urgent',
            'status'      => 'required|in:todo,in_progress,review,done',
            'due_date'    => 'nullable|date',
        ]);

        if ($validated['status'] === 'done') {
            $validated['completed_at'] = now();
        }

        $task = $project->tasks()->create($validated);
        $project->updateAutoProgress();

        AuditLogger::log(
            action: 'create',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Menambahkan Task '{$task->title}' pada Proyek '{$project->name}'",
            newData: ['task_title' => $task->title, 'status' => $task->status]
        );

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, ProjectTask $task)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:admins,id',
            'priority'    => 'required|in:low,normal,high,urgent',
            'status'      => 'required|in:todo,in_progress,review,done',
            'due_date'    => 'nullable|date',
        ]);

        if ($validated['status'] === 'done' && $task->status !== 'done') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'done') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);
        $project = $task->project;
        $project->updateAutoProgress();

        AuditLogger::log(
            action: 'update',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Memperbarui Task '{$task->title}'",
            newData: ['status' => $task->status]
        );

        return redirect()->back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(ProjectTask $task)
    {
        $project = $task->project;
        $title = $task->title;

        $task->delete();
        $project->updateAutoProgress();

        AuditLogger::log(
            action: 'delete',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Menghapus Task '{$title}' dari Proyek '{$project->name}'"
        );

        return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
    }
}
