<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('creator', 'assignedStaff', 'client', 'quotation', 'team');

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

        $projects = $query->latest()->paginate(15)->withQueryString();
        return view('admin.projects.index', compact('projects'));
    }

    public function kanban()
    {
        $projects = Project::with('client', 'assignedStaff', 'tasks')->get();

        $kanbanColumns = [
            'planning'    => $projects->where('status', 'planning'),
            'in_progress' => $projects->filter(fn($p) => in_array($p->status, ['in_progress', 'ongoing'])),
            'review'      => $projects->where('status', 'review'),
            'completed'   => $projects->where('status', 'completed'),
            'on_hold'     => $projects->filter(fn($p) => in_array($p->status, ['on_hold', 'pending'])),
        ];

        return view('admin.projects.kanban', compact('kanbanColumns'));
    }

    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'status' => 'required|in:planning,in_progress,review,completed,on_hold,cancelled',
        ]);

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
            description: "Mengubah status proyek '{$project->name}' dari {$oldStatus} ke {$project->status}",
            oldData: ['status' => $oldStatus],
            newData: ['status' => $project->status]
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status proyek berhasil diperbarui.']);
        }

        return redirect()->back()->with('success', 'Status proyek berhasil diperbarui.');
    }

    public function create()
    {
        $admins = Admin::where('status', 'active')->get();
        $clients = Client::where('status', 'active')->orWhere('status', 'prospect')->get();
        $quotations = Quotation::where('status', 'accepted')->whereNull('project_id')->get();

        // Auto Generate Project Number
        $prefix = 'PRJ-' . date('Ym') . '-';
        $latest = Project::where('project_number', 'like', "{$prefix}%")->orderBy('id', 'desc')->first();
        $number = 1;
        if ($latest && $latest->project_number) {
            $parts = explode('-', $latest->project_number);
            $number = ((int) end($parts)) + 1;
        }
        $autoProjectNumber = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);

        return view('admin.projects.create', compact('admins', 'clients', 'quotations', 'autoProjectNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
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

        $data = $request->except(['team_members', 'team_roles']);
        $data['admin_id'] = auth('admin')->id();

        if (empty($data['end_date']) && !empty($data['deadline'])) {
            $data['end_date'] = $data['deadline'];
        }

        $project = Project::create($data);

        // Attach team members
        if ($request->has('team_members') && count($request->team_members) > 0) {
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
            description: "Membuat proyek baru: {$project->name}",
            newData: ['name' => $project->name, 'status' => $project->status]
        );

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil dibuat.');
    }

    public function show(Project $project)
    {
        $project->load(['creator', 'assignedStaff', 'client', 'quotation', 'team', 'tasks.assignee', 'documents.uploader']);
        $admins = Admin::where('status', 'active')->get();

        $activityLogs = AuditLog::where('module', 'Projects')
            ->where('record_id', (string) $project->id)
            ->latest()
            ->take(15)
            ->get();

        return view('admin.projects.show', compact('project', 'admins', 'activityLogs'));
    }

    public function edit(Project $project)
    {
        $admins = Admin::where('status', 'active')->get();
        $clients = Client::where('status', 'active')->orWhere('status', 'prospect')->get();
        $quotations = Quotation::where('status', 'accepted')->get();
        $project->load('team');

        return view('admin.projects.edit', compact('project', 'admins', 'clients', 'quotations'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
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

        $oldData = $project->only(['name', 'status', 'progress']);
        $data = $request->except(['team_members', 'team_roles']);

        if (empty($data['end_date']) && !empty($data['deadline'])) {
            $data['end_date'] = $data['deadline'];
        }

        $project->update($data);

        // Sync team members
        if ($request->has('team_members')) {
            $teamData = [];
            foreach ($request->team_members as $key => $memberId) {
                if ($memberId) {
                    $teamData[$memberId] = [
                        'role' => $request->team_roles[$key] ?? null
                    ];
                }
            }
            $project->team()->sync($teamData);
        } else {
            $project->team()->detach();
        }

        AuditLogger::log(
            action: 'update',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Memperbarui data proyek: {$project->name}",
            oldData: $oldData,
            newData: ['name' => $project->name, 'status' => $project->status]
        );

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        AuditLogger::log(
            action: 'delete',
            module: 'Projects',
            recordId: (string) $project->id,
            description: "Menghapus proyek: {$project->name}",
            oldData: ['name' => $project->name]
        );

        $project->team()->detach();
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil dihapus.');
    }
}