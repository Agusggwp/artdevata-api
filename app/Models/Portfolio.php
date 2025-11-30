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
        'link',

        // FIELD BARU
        'category',
        'client',
        'date',
        'duration',
        'challenge',
        'solution',

        // JSON FIELDS
        'results',
        'technologies',
        'images',
    ];

    /**
     * Cast JSON fields to array automatically
     */
    protected $casts = [
        'results' => 'array',
        'technologies' => 'array',
        'images' => 'array',
    ];

    /**
     * Accessor untuk link (tetap aman)
     */
    public function getLinkAttribute($value)
    {
        return $value ?: '#';
    }

    /**
     * Untuk menampilkan link dengan format rapi
     */
    public function linkLabel()
    {
        if (!$this->link || $this->link === '#') {
            return 'Tidak ada tautan';
        }

        return strlen($this->link) > 40
            ? substr($this->link, 0, 40) . '...'
            : $this->link;
    }
}
