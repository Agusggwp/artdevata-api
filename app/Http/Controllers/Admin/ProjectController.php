<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Admin;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('creator', 'team')->latest()->paginate(15);
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $admins = Admin::all();
        return view('admin.projects.create', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:ongoing,completed,pending',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'client' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric',
            'progress' => 'nullable|integer|min:0|max:100',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:admins,id',
            'team_roles' => 'nullable|array',
            'team_roles.*' => 'nullable|string'
        ]);

        $project = Project::create($request->all() + ['admin_id' => auth('admin')->id()]);

        // Attach team members dengan role
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

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil dibuat');
    }

    public function show(Project $project)
    {
        $project->load('creator', 'team');
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $admins = Admin::all();
        $project->load('team');
        return view('admin.projects.edit', compact('project', 'admins'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:ongoing,completed,pending',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'client' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric',
            'progress' => 'nullable|integer|min:0|max:100',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:admins,id',
            'team_roles' => 'nullable|array',
            'team_roles.*' => 'nullable|string'
        ]);

        $project->update($request->all());

        // Sync team members dengan role
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

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil diperbarui');
    }

    public function destroy(Project $project)
    {
        $project->team()->detach();
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil dihapus');
    }
}