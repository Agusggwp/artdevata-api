<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'source',
        'service_interest',
        'estimated_budget',
        'notes',
        'status',
        'assigned_to',
        'client_id',
        'next_follow_up_at',
    ];

    protected $casts = [
        'estimated_budget' => 'decimal:2',
        'next_follow_up_at' => 'datetime',
    ];

    public function assignedStaff()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'new' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-900 border border-sky-300">New</span>',
            'contacted' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-300">Contacted</span>',
            'qualified' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-900 border border-indigo-300">Qualified</span>',
            'negotiation' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">Negotiation</span>',
            'won' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">Won</span>',
            'lost' => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-900 border border-rose-300">Lost</span>',
            default => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-900 border border-slate-300">Unknown</span>',
        };
    }
}
