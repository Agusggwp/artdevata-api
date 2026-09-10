<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'project_id',
        'client_name',
        'client_email',
        'client_address',
        'items',
        'subtotal',
        'tax',
        'total',
        'status',
        'invoice_date',
        'due_date',
        'notes',
        'admin_id',
    ];

    protected $casts = [
        'items' => 'array',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-900 border border-slate-300">Draft</span>',
            'sent' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-900 border border-blue-300">Sent</span>',
            'paid' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300">Paid</span>',
            'overdue' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-900 border border-rose-300">Overdue</span>',
            default => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-900 border border-slate-300">Draft</span>',
        };
    }
}