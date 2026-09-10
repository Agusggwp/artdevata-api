<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-900 border border-slate-300">Draft</span>',
            'sent' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-900 border border-blue-300">Sent</span>',
            'paid' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300">Paid</span>',
            'overdue' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-900 border border-rose-300">Overdue</span>',
        };
    }
}