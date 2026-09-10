<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_number',
        'client_id',
        'quotation_id',
        'name',
        'description',
        'status',
        'priority',
        'start_date',
        'end_date',
        'deadline',
        'completed_at',
        'client',
        'budget',
        'assigned_to',
        'progress',
        'notes',
        'admin_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deadline' => 'date',
        'completed_at' => 'datetime',
        'budget' => 'decimal:2',
        'progress' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function team()
    {
        return $this->belongsToMany(Admin::class, 'project_admin')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function updateAutoProgress()
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return;
        }
        $completed = $this->tasks()->where('status', 'done')->count();
        $this->progress = (int) round(($completed / $total) * 100);
        $this->save();
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'planning' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-900 border border-sky-300">Planning</span>',
            'in_progress', 'ongoing' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-300">In Progress</span>',
            'review' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-900 border border-purple-300">Review</span>',
            'completed' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">Completed</span>',
            'on_hold', 'pending' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">On Hold</span>',
            'cancelled' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-900 border border-rose-300">Cancelled</span>',
            default => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-900 border border-slate-300">Unknown</span>',
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'low' => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">Low</span>',
            'normal' => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-100 text-sky-800 border border-sky-200">Normal</span>',
            'high' => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">High</span>',
            'urgent' => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800 border border-rose-200">Urgent</span>',
            default => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">Normal</span>',
        };
    }
}