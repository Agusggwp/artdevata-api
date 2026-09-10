<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'client',
        'budget',
        'progress',
        'admin_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'progress' => 'integer'
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    // Relasi many-to-many: siapa saja yang mengerjakan proyek ini
    public function team()
    {
        return $this->belongsToMany(Admin::class, 'project_admin')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'ongoing' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-300">Sedang Berjalan</span>',
            'completed' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">Selesai</span>',
            'pending' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">Tertunda</span>',
            default => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-900 border border-slate-300">Unknown</span>'
        };
    }
}