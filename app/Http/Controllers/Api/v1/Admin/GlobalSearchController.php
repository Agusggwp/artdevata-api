<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Quotation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    use ApiResponseTrait;

    public function search(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));

        if (empty($query) || strlen($query) < 2) {
            return $this->successResponse([
                'leads'      => [],
                'clients'    => [],
                'quotations' => [],
                'projects'   => [],
                'invoices'   => [],
            ], 'Hasil pencarian kosong.');
        }

        $leads = Lead::where('name', 'like', "%{$query}%")
            ->orWhere('company_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'company_name', 'email', 'status']);

        $clients = Client::where('name', 'like', "%{$query}%")
            ->orWhere('company_name', 'like', "%{$query}%")
            ->orWhere('company', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'company_name', 'email', 'status']);

        $quotations = Quotation::with('client')
            ->where('quotation_number', 'like', "%{$query}%")
            ->orWhereHas('client', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%");
            })
            ->take(5)
            ->get();

        $projects = Project::where('name', 'like', "%{$query}%")
            ->orWhere('project_number', 'like', "%{$query}%")
            ->orWhere('client', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'project_number', 'status', 'progress']);

        $invoices = Invoice::where('invoice_number', 'like', "%{$query}%")
            ->orWhere('client_name', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'invoice_number', 'client_name', 'total', 'status']);

        return $this->successResponse([
            'leads' => $leads->map(fn($item) => [
                'id'       => $item->id,
                'title'    => $item->name . ($item->company_name ? " ({$item->company_name})" : ''),
                'subtitle' => 'Lead • ' . ucfirst($item->status),
                'type'     => 'lead',
            ]),
            'clients' => $clients->map(fn($item) => [
                'id'       => $item->id,
                'title'    => $item->name . ($item->company_name ? " ({$item->company_name})" : ''),
                'subtitle' => 'Client • ' . ucfirst($item->status),
                'type'     => 'client',
            ]),
            'quotations' => $quotations->map(fn($item) => [
                'id'       => $item->id,
                'title'    => $item->quotation_number . ' - ' . ($item->client?->name ?? 'N/A'),
                'subtitle' => 'Quotation • Rp ' . number_format($item->total, 0, ',', '.') . ' (' . ucfirst($item->status) . ')',
                'type'     => 'quotation',
            ]),
            'projects' => $projects->map(fn($item) => [
                'id'       => $item->id,
                'title'    => ($item->project_number ? "{$item->project_number} - " : '') . $item->name,
                'subtitle' => 'Project • Progress ' . $item->progress . '% (' . ucfirst($item->status) . ')',
                'type'     => 'project',
            ]),
            'invoices' => $invoices->map(fn($item) => [
                'id'       => $item->id,
                'title'    => $item->invoice_number . ' - ' . $item->client_name,
                'subtitle' => 'Invoice • Rp ' . number_format($item->total, 0, ',', '.') . ' (' . ucfirst($item->status) . ')',
                'type'     => 'invoice',
            ]),
        ], 'Hasil pencarian berhasil diambil.');
    }
}
