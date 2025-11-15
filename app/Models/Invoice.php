<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'client_name', 'client_email', 
        'items', 'total', 'due_date', 'status'
    ];

    protected $casts = [
        'items' => 'array',
        'due_date' => 'date',
    ];

    public function totalFormat()
    {
        return 'Rp ' . number_format($this->total);
    }
}