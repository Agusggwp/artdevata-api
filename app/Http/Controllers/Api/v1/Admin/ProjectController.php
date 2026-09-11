<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Project;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Project::with('creator:id,name', 'assignedStaff:id,name,email', 'client:id,name,company_name', 'quotation:id,quotation_number', 'team:id,name,email');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_number', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $perPage = $request->get('per_page', 15);
        $projects = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($projects, 'Daftar proyek berhasil diambil.');
    }

    public function kanban(): JsonResponse
    {
        $projects = Project::with('client:id,name', 'assignedStaff:id,name', 'tasks')->get();

        $kanbanColumns = [
            'planning'    => $projects->where('status', 'planning')->values(),
            'in_progress' => $projects->filter(fn($p) => in_array($p->status, ['in_progress', 'ongoing']))->values(),
            'review'      => $projects->where('status', 'review')->values(),
            'completed'   => $projects->where('status', 'completed')->values(),
            'on_hold'     => $projects->filter(fn($p) => in_array($p->status, ['on_hold', 'pending']))->values(),
        ];

        return $this->successResponse($kanbanColumns, 'Data kanban proyek berhasil diambil.');
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $project = Project::find($id);

        if (!$project) {
            return $this->errorResponse('Proyek tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:planning,in_progress,review,completed,on_hold,cancelled,ongoing,pending',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $oldStatus = $project->status;
        $project->status = $request->status;
        if ($request->status === 'completed' && !$project->completed_at) {
            $project->completed_at = now();
            $project->progress = 100;
        }
        $project->save();

        AuditLogger::log(
            action: 'update',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Mengubah status proyek (API) '{$project->name}' dari {$oldStatus} ke {$project->status}",
            oldData: ['status' => $oldStatus],
            newData: ['status' => $project->status]
        );

        return $this->successResponse($project, 'Status proyek berhasil diperbarui.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_number' => 'nullable|string|max:100|unique:projects,project_number',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'client_id'      => 'nullable|exists:clients,id',
            'quotation_id'   => 'nullable|exists:quotations,id',
            'status'         => 'required|in:planning,in_progress,review,completed,on_hold,cancelled,ongoing,pending',
            'priority'       => 'required|in:low,normal,high,urgent',
            'start_date'     => 'nullable|date',
            'deadline'       => 'nullable|date',
            'end_date'       => 'nullable|date',
            'client'         => 'nullable|string|max:255',
            'budget'         => 'nullable|numeric|min:0',
            'assigned_to'    => 'nullable|exists:admins,id',
            'progress'       => 'nullable|integer|min:0|max:100',
            'notes'          => 'nullable|string',
            'team_members'   => 'nullable|array',
            'team_members.*' => 'exists:admins,id',
            'team_roles'     => 'nullable|array',
            'team_roles.*'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $data = $request->except(['team_members', 'team_roles']);
        $data['admin_id'] = $request->user()?->id;

        if (empty($data['project_number'])) {
            $prefix = 'PRJ-' . date('Ym') . '-';
            $latest = Project::where('project_number', 'like', "{$prefix}%")->orderBy('id', 'desc')->first();
            $number = 1;
            if ($latest && $latest->project_number) {
                $parts = explode('-', $latest->project_number);
                $number = ((int) end($parts)) + 1;
            }
            $data['project_number'] = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        }

        if (empty($data['end_date']) && !empty($data['deadline'])) {
            $data['end_date'] = $data['deadline'];
        }

        $project = Project::create($data);

        if ($request->has('team_members') && is_array($request->team_members)) {
            $teamData = [];
            foreach ($request->team_members as $key => $memberId) {
                if ($memberId) {
                    $teamData[$memberId] = [
                        'role' => $request->team_roles[$key] ?? null
                    ];
                }
            }
            $project->team()->attach($teamData);
        }

        AuditLogger::log(
            action: 'create',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Membuat proyek baru (API): {$project->name}",
            newData: ['name' => $project->name, 'status' => $project->status]
        );

        return $this->successResponse($project->load('creator', 'assignedStaff', 'client', 'team'), 'Proyek berhasil dibuat.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $project = Project::with(['creator', 'assignedStaff', 'client', 'quotation', 'team', 'tasks.assignee', 'documents.uploader'])->find($id);

        if (!$project) {
            return $this->errorResponse('Proyek tidak ditemukan.', 404);
        }

        $logs = AuditLog::where('module', 'Projects')
            ->where('record_id', (string) $project->id)
            ->latest()
            ->take(15)
            ->get();

        return $this->successResponse([
            'project'       => $project,
            'activity_logs' => $logs
        ], 'Detail proyek berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $project = Project::find($id);

        if (!$project) {
            return $this->errorResponse('Proyek tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'project_number' => 'nullable|string|max:100|unique:projects,project_number,' . $project->id,
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'client_id'      => 'nullable|exists:clients,id',
            'quotation_id'   => 'nullable|exists:quotations,id',
            'status'         => 'required|in:planning,in_progress,review,completed,on_hold,cancelled,ongoing,pending',
            'priority'       => 'required|in:low,normal,high,urgent',
            'start_date'     => 'nullable|date',
            'deadline'       => 'nullable|date',
            'end_date'       => 'nullable|date',
            'client'         => 'nullable|string|max:255',
            'budget'         => 'nullable|numeric|min:0',
            'assigned_to'    => 'nullable|exists:admins,id',
            'progress'       => 'nullable|integer|min:0|max:100',
            'notes'          => 'nullable|string',
            'team_members'   => 'nullable|array',
            'team_members.*' => 'exists:admins,id',
            'team_roles'     => 'nullable|array',
            'team_roles.*'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $oldData = $project->only(['name', 'status', 'progress']);
        $data = $request->except(['team_members', 'team_roles']);

        if (empty($data['end_date']) && !empty($data['deadline'])) {
            $data['end_date'] = $data['deadline'];
        }

        $project->update($data);

        if ($request->has('team_members') && is_array($request->team_members)) {
            $teamData = [];
            foreach ($request->team_members as $key => $memberId) {
                if ($memberId) {
                    $teamData[$memberId] = [
                        'role' => $request->team_roles[$key] ?? null
                    ];
                }
            }
            $project->team()->sync($teamData);
        }

        AuditLogger::log(
            action: 'update',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Memperbarui data proyek (API): {$project->name}",
            oldData: $oldData,
            newData: ['name' => $project->name, 'status' => $project->status]
        );

        return $this->successResponse($project->load('creator', 'assignedStaff', 'client', 'team'), 'Proyek berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $project = Project::find($id);

        if (!$project) {
            return $this->errorResponse('Proyek tidak ditemukan.', 404);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Menghapus proyek (API): {$project->name}",
            oldData: ['name' => $project->name]
        );

        $project->team()->detach();
        $project->delete();

        return $this->successResponse(null, 'Proyek berhasil dihapus.');
    }
}
