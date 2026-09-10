<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'last_login_at',
        'last_login_ip',
        'last_activity_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * Check if admin is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if admin has Super Admin role.
     */
    public function isSuperAdmin(): bool
    {
        if ($this->role && $this->role->slug === 'super-admin') {
            return true;
        }
        // Fallback for default initial admin without role explicitly assigned yet
        return $this->id === 1 && (!$this->role || $this->role->slug === 'super-admin');
    }

    /**
     * Check if admin has specific role by slug.
     */
    public function hasRole(string $roleSlug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Check if admin has specific permission by slug.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Super Admin has emergency/full access to everything
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions->contains('slug', $permissionSlug);
    }
}