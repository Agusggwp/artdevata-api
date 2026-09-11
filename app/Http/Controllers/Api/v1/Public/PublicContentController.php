<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Client;
use App\Models\Documentation;
use App\Models\Lead;
use App\Models\Portfolio;
use App\Models\Service;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PublicContentController extends Controller
{
    use ApiResponseTrait;

    public function services(): JsonResponse
    {
        $services = Service::latest()->get()->map(function ($service) {
            return [
                'id'          => $service->id,
                'title'       => $service->title,
                'description' => $service->description,
                'image_url'   => $service->image ? asset('storage/' . $service->image) : null,
                'features'    => $service->features ?? [],
                'created_at'  => $service->created_at,
            ];
        });

        return $this->successResponse($services, 'Data layanan berhasil diambil.');
    }

    public function serviceShow(string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->errorResponse('Layanan tidak ditemukan.', 404);
        }

        $serviceData = [
            'id'          => $service->id,
            'title'       => $service->title,
            'description' => $service->description,
            'image_url'   => $service->image ? asset('storage/' . $service->image) : null,
            'features'    => $service->features ?? [],
            'created_at'  => $service->created_at,
        ];

        return $this->successResponse($serviceData, 'Detail layanan berhasil diambil.');
    }

    public function portfolios(Request $request): JsonResponse
    {
        $query = Portfolio::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $portfolios = $query->latest()->get()->map(function ($p) {
            return [
                'id'           => $p->id,
                'title'        => $p->title,
                'description'  => $p->description,
                'client_name'  => $p->client_name,
                'category'     => $p->category,
                'project_date' => $p->project_date,
                'link'         => $p->link,
                'image_url'    => $p->image ? asset('storage/' . $p->image) : null,
                'created_at'   => $p->created_at,
            ];
        });

        return $this->successResponse($portfolios, 'Data portofolio berhasil diambil.');
    }

    public function portfolioShow(string $id): JsonResponse
    {
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return $this->errorResponse('Portofolio tidak ditemukan.', 404);
        }

        $portfolioData = [
            'id'           => $portfolio->id,
            'title'        => $portfolio->title,
            'description'  => $portfolio->description,
            'client_name'  => $portfolio->client_name,
            'category'     => $portfolio->category,
            'project_date' => $portfolio->project_date,
            'link'         => $portfolio->link,
            'image_url'    => $portfolio->image ? asset('storage/' . $portfolio->image) : null,
            'created_at'   => $portfolio->created_at,
        ];

        return $this->successResponse($portfolioData, 'Detail portofolio berhasil diambil.');
    }

    public function blogs(Request $request): JsonResponse
    {
        $query = Blog::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $blogs = $query->latest()->paginate($request->get('per_page', 10));

        $blogs->getCollection()->transform(function ($b) {
            return [
                'id'         => $b->id,
                'title'      => $b->title,
                'slug'       => $b->slug ?? Str::slug($b->title),
                'excerpt'    => Str::limit(strip_tags($b->content), 150),
                'content'    => $b->content,
                'image_url'  => $b->image ? asset('storage/' . $b->image) : null,
                'category'   => $b->category ?? 'Umum',
                'author'     => $b->author ?? 'Tim ArtDevata',
                'created_at' => $b->created_at,
            ];
        });

        return $this->paginatedResponse($blogs, 'Daftar artikel blog berhasil diambil.');
    }

    public function blogShow(string $idOrSlug): JsonResponse
    {
        $blog = Blog::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$blog) {
            return $this->errorResponse('Artikel blog tidak ditemukan.', 404);
        }

        $blogData = [
            'id'         => $blog->id,
            'title'      => $blog->title,
            'slug'       => $blog->slug ?? Str::slug($blog->title),
            'excerpt'    => Str::limit(strip_tags($blog->content), 150),
            'content'    => $blog->content,
            'image_url'  => $blog->image ? asset('storage/' . $blog->image) : null,
            'category'   => $blog->category ?? 'Umum',
            'author'     => $blog->author ?? 'Tim ArtDevata',
            'created_at' => $blog->created_at,
        ];

        return $this->successResponse($blogData, 'Detail artikel blog berhasil diambil.');
    }

    public function clients(): JsonResponse
    {
        $clients = Client::where('status', 'active')->get()->map(function ($c) {
            return [
                'id'           => $c->id,
                'name'         => $c->name,
                'company_name' => $c->company_name ?? $c->company,
                'logo_url'     => $c->logo ? asset('storage/' . $c->logo) : null,
                'website'      => $c->website,
            ];
        });

        return $this->successResponse($clients, 'Data klien berhasil diambil.');
    }

    public function documentations(): JsonResponse
    {
        $documentations = Documentation::where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(function ($d) {
                return [
                    'id'          => $d->id,
                    'title'       => $d->title,
                    'category'    => $d->category,
                    'description' => $d->description,
                    'image_url'   => $d->image ? asset('storage/' . $d->image) : null,
                    'sort_order'  => $d->sort_order,
                ];
            });

        return $this->successResponse($documentations, 'Dokumentasi publik berhasil diambil.');
    }

    /**
     * Public Contact Form submission (Creates a new Lead Prospek)
     */
    public function submitLead(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'service_interest' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi pesan gagal.', 422, $validator->errors());
        }

        $lead = Lead::create([
            'name'             => $request->name,
            'company_name'     => $request->company_name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'source'           => 'website',
            'service_interest' => $request->service_interest,
            'estimated_budget' => $request->estimated_budget,
            'notes'            => $request->notes,
            'status'           => 'new',
        ]);

        return $this->successResponse([
            'lead_id' => $lead->id,
            'name'    => $lead->name,
            'email'   => $lead->email,
        ], 'Pesan & formulir konsultasi Anda berhasil dikirim. Tim kami akan segera menghubungi Anda.', 201);
    }
}
