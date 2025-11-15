<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    // GET /api/services → semua data
    public function index()
    {
        $services = Service::select('id', 'title', 'description', 'image')->get();
        return response()->json($services);
    }

    // GET /api/services/1 → satu data
    public function show($id)
    {
        $service = Service::select('id', 'title', 'description', 'image')
                          ->findOrFail($id);
        return response()->json($service);
    }
}