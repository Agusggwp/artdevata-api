<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * GET /api/clients
     * Mengembalikan daftar client aktif dengan logo
     */
    public function index()
    {
        $clients = Client::where('status', 'active')
            ->select('id', 'name', 'company', 'logo')
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'company' => $client->company,
                    'logo' => $client->logo ? asset('storage/' . $client->logo) : null,
                ];
            });

        return response()->json([
            'data' => $clients
        ]);
    }
}
