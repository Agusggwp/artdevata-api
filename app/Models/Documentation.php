<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasFactory;

    protected $table = 'documentations';

    protected $fillable = [
        'title',
        'category',
        'description',
        'image',
        'status',
        'sort_order',
    ];

    /**
     * Helper accessor for full image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }
        return null;
    }
}
