<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;

class PortfolioController extends Controller
{
    /**
     * GET /api/portfolios
     * Semua portfolio
     */
    public function index()
    {
        $portfolios = Portfolio::latest()->get()->map(function ($item) {

            // Format main image
            $mainImage = $item->image ? asset('storage/' . $item->image) : null;

            // Format gallery images
            $gallery = [];
            if (is_array($item->images)) {
                foreach ($item->images as $img) {
                    $gallery[] = asset('storage/' . $img);
                }
            }

            return [
                'id'           => $item->id,
                'title'        => $item->title,
                'description'  => $item->description,
                'image'        => $mainImage,
                'link'         => $item->link,
                'category'     => $item->category,
                'client'       => $item->client,
                'date'         => $item->date,
                'duration'     => $item->duration,
                'challenge'    => $item->challenge,
                'solution'     => $item->solution,
                'results'      => $item->results ?? [],
                'technologies' => $item->technologies ?? [],
                'images'       => $gallery,
            ];
        });

        return response()->json($portfolios);
    }

    /**
     * GET /api/portfolios/{id}
     * Detail portfolio
     */
    public function show($id)
    {
        $item = Portfolio::findOrFail($id);

        // Format main image
        $mainImage = $item->image ? asset('storage/' . $item->image) : null;

        // Format gallery images
        $gallery = [];
        if (is_array($item->images)) {
            foreach ($item->images as $img) {
                $gallery[] = asset('storage/' . $img);
            }
        }

        $portfolio = [
            'id'           => $item->id,
            'title'        => $item->title,
            'description'  => $item->description,
            'image'        => $mainImage,
            'link'         => $item->link,
            'category'     => $item->category,
            'client'       => $item->client,
            'date'         => $item->date,
            'duration'     => $item->duration,
            'challenge'    => $item->challenge,
            'solution'     => $item->solution,
            'results'      => $item->results ?? [],
            'technologies' => $item->technologies ?? [],
            'images'       => $gallery,
        ];

        return response()->json($portfolio);
    }
}
