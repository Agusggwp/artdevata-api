<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display centralized Audit / Activity Logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('admin');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admin_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $logs = $query->latest('created_at')->paginate(20);
        $admins = Admin::all();
        $modules = AuditLog::select('module')->distinct()->pluck('module');
        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.activity_logs.index', compact('logs', 'admins', 'modules', 'actions'));
    }
}
