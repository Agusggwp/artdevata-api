<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
