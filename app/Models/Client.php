<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'logo',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi dengan Project (jika diperlukan)
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // Relasi dengan Invoice (jika diperlukan)
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
