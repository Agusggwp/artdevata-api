<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;

class PortfolioController extends Controller
{
    /**
     * GET /api/portfolios
     * → Semua portfolio + link
     */
    public function index()
    {
        $portfolios = Portfolio::select('id', 'title', 'description', 'image', 'link')
                               ->get();

        // Format image & link agar full URL
        $portfolios = $portfolios->map(function ($item) {
            $item->image = $item->image ? asset('storage/' . $item->image) : null;
            $item->link = $item->link ?: null;
            return $item;
        });

        return response()->json($portfolios);
    }

    /**
     * GET /api/portfolios/{id}
     * → Satu portfolio + link
     */
    public function show($id)
    {
        $portfolio = Portfolio::select('id', 'title', 'description', 'image', 'link')
                              ->findOrFail($id);

        // Format image & link
        $portfolio->image = $portfolio->image ? asset('storage/' . $portfolio->image) : null;
        $portfolio->link = $portfolio->link ?: null;

        return response()->json($portfolio);
    }
}