<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link' // ← TAMBAHAN TAUTAN
    ];

    /**
     * Akses link dengan aman
     */
    public function getLinkAttribute($value)
    {
        return $value ? $value : '#';
    }

    /**
     * Format link untuk tampilan
     */
    public function linkLabel()
    {
        if (!$this->link || $this->link === '#') {
            return 'Tidak ada tautan';
        }
        return strlen($this->link) > 40 ? substr($this->link, 0, 40) . '...' : $this->link;
    }
}