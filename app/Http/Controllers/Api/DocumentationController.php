<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * GET /api/documentations
     * Returns list of active company documentations for public website / frontend.
     */
    public function index(Request $request)
    {
        $query = Documentation::where('status', 'active');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $documentations = $query->orderBy('sort_order', 'asc')
                                ->latest()
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'id'          => $item->id,
                                        'title'       => $item->title,
                                        'category'    => $item->category,
                                        'description' => $item->description,
                                        'image'       => $item->image_url,
                                        'sort_order'  => $item->sort_order,
                                        'created_at'  => $item->created_at,
                                    ];
                                });

        return response()->json([
            'status'  => 'success',
            'message' => 'Data dokumentasi perusahaan berhasil diambil.',
            'data'    => $documentations,
        ]);
    }

    /**
     * GET /api/documentations/{id}
     * Returns single documentation item.
     */
    public function show($id)
    {
        $documentation = Documentation::where('status', 'active')->findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail dokumentasi berhasil diambil.',
            'data'    => [
                'id'          => $documentation->id,
                'title'       => $documentation->title,
                'category'    => $documentation->category,
                'description' => $documentation->description,
                'image'       => $documentation->image_url,
                'sort_order'  => $documentation->sort_order,
                'created_at'  => $documentation->created_at,
            ],
        ]);
    }
}
