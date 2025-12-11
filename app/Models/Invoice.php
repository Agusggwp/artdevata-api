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
            'draft' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-800">Draft</span>',
            'sent' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-200 text-blue-800">Sent</span>',
            'paid' => '<span class="px-2 py-1 text-xs rounded-full bg-green-200 text-green-800">Paid</span>',
            'overdue' => '<span class="px-2 py-1 text-xs rounded-full bg-red-200 text-red-800">Overdue</span>',
        };
    }
}