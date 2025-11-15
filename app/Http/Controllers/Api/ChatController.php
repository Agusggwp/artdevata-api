<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string'
        ]);

        $msg = Message::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Pesan diterima! Kami akan segera balas.'
        ], 201);
    }
}