<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginHistoryController extends Controller
{
    /**
     * Display Login Histories.
     */
    public function index(Request $request)
    {
        $currentAdmin = Auth::guard('admin')->user();
        $query = LoginHistory::with('admin');

        // Non-Super Admins can only see their own login history
        if (!$currentAdmin->isSuperAdmin()) {
            $query->where(function ($q) use ($currentAdmin) {
                $q->where('admin_id', $currentAdmin->id)
                  ->orWhere('email', $currentAdmin->email);
            });
        } elseif ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $histories = $query->latest('created_at')->paginate(20);
        $admins = $currentAdmin->isSuperAdmin() ? Admin::all() : collect([$currentAdmin]);

        return view('admin.login_histories.index', compact('histories', 'admins'));
    }
}
