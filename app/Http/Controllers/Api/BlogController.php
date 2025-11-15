<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;

class BlogController extends Controller
{
    // GET /api/blogs → semua artikel
    public function index()
    {
        $blogs = Blog::select('id', 'title', 'content', 'image', 'created_at')
                     ->orderBy('created_at', 'desc')
                     ->get();
        return response()->json($blogs);
    }

    // GET /api/blogs/1 → satu artikel
    public function show($id)
    {
        $blog = Blog::select('id', 'title', 'content', 'image', 'created_at')
                    ->findOrFail($id);
        return response()->json($blog);
    }
}