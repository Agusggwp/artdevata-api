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
            'ongoing' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Sedang Berjalan</span>',
            'completed' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Selesai</span>',
            'pending' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Tertunda</span>',
            default => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Unknown</span>'
        };
    }
}