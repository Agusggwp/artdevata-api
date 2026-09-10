<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'priority',
        'status',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'low' => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">Low</span>',
            'normal' => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-100 text-sky-800">Normal</span>',
            'high' => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">High</span>',
            'urgent' => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800">Urgent</span>',
            default => '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-800">Normal</span>',
        };
    }
}
