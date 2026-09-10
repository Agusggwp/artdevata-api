<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Keys to automatically scrub from old/new data.
     */
    protected static array $sensitiveKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'secret',
        'api_key',
        'remember_token',
    ];

    /**
     * Log an admin action to audit_logs table.
     */
    public static function log(
        string $action,
        string $module,
        ?string $recordId = null,
        ?string $description = null,
        ?array $oldData = null,
        ?array $newData = null
    ): AuditLog {
        $admin = Auth::guard('admin')->user();

        return AuditLog::create([
            'admin_id'    => $admin?->id,
            'admin_name'  => $admin?->name ?? 'System',
            'action'      => $action,
            'module'      => $module,
            'record_id'   => $recordId,
            'description' => $description,
            'old_data'    => static::sanitizeData($oldData),
            'new_data'    => static::sanitizeData($newData),
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'created_at'  => now(),
        ]);
    }

    /**
     * Recursively remove sensitive keys from array data.
     */
    protected static function sanitizeData(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $sanitized = [];

        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), static::$sensitiveKeys, true)) {
                $sanitized[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $sanitized[$key] = static::sanitizeData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
