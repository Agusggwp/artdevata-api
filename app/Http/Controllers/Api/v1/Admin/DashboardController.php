<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CompanyTransaction;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SalaryPayment;
use App\Models\Service;
use App\Models\Blog;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get complete dashboard metrics & overview data.
     */
    public function index(Request $request): JsonResponse
    {
        // Invoices metrics
        $totalPaidAmount = (float) Invoice::where('status', 'paid')->sum('total');
        $paidInvoiceCount = Invoice::where('status', 'paid')->count();
        $totalInvoiceAmount = (float) Invoice::sum('total');
        $totalInvoiceCount = Invoice::count();

        // Core Counts
        $totalBlogs = Blog::count();
        $totalPortfolios = Portfolio::count();
        $totalServices = Service::count();
        $totalClients = Client::count();

        // Project Status Counts
        $totalProjects = Project::count();
        $ongoingProjects = Project::whereIn('status', ['ongoing', 'in_progress', 'planning'])->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $pendingProjects = Project::whereIn('status', ['pending', 'on_hold', 'review'])->count();

        // CRM / Leads Metrics
        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'new')->count();
        $wonLeads = Lead::where('status', 'won')->count();
        $conversionRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0;

        $activeClients = Client::where('status', 'active')->count();
        $activeProjects = Project::whereIn('status', ['planning', 'in_progress', 'ongoing', 'review'])->count();

        $pendingQuotations = Quotation::whereIn('status', ['draft', 'sent', 'viewed'])->count();
        $acceptedQuotations = Quotation::where('status', 'accepted')->count();

        // Pipeline Value
        $leadsPipeline = (float) Lead::whereIn('status', ['new', 'contacted', 'qualified', 'negotiation'])->sum('estimated_budget');
        $quotationsPipeline = (float) Quotation::whereIn('status', ['draft', 'sent', 'viewed'])->sum('total');
        $pipelineValue = $leadsPipeline + $quotationsPipeline;

        // Pipeline breakdown
        $pipelineByStatus = [
            'new'         => Lead::where('status', 'new')->count(),
            'contacted'   => Lead::where('status', 'contacted')->count(),
            'qualified'   => Lead::where('status', 'qualified')->count(),
            'negotiation' => Lead::where('status', 'negotiation')->count(),
            'won'         => Lead::where('status', 'won')->count(),
            'lost'        => Lead::where('status', 'lost')->count(),
        ];

        // Monthly Leads Chart Data
        $monthlyLeads = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthName = $monthDate->format('M Y');
            $count = Lead::whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month)
                ->count();
            $wonCount = Lead::whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month)
                ->where('status', 'won')
                ->count();
            $monthlyLeads[] = [
                'month' => $monthName,
                'total' => $count,
                'won'   => $wonCount,
            ];
        }

        // Financial Net Balance Calculation
        $transactionsNet = (float) CompanyTransaction::selectRaw(
            "COALESCE(SUM(CASE WHEN type='credit' THEN amount WHEN type='debit' THEN -amount ELSE 0 END),0) as net"
        )->value('net');

        $totalPaidInvoices = (float) Invoice::where('status', 'paid')->sum('total');
        $totalSalariesPaid = (float) SalaryPayment::where('status', 'paid')->sum('amount');
        $companyBalance = round($totalPaidInvoices - $totalSalariesPaid + $transactionsNet, 2);

        $recentTransactions = CompanyTransaction::with('admin:id,name')->latest()->take(6)->get();
        $recentLeads = Lead::latest()->take(5)->get();
        $recentProjects = Project::with('client:id,name')->latest()->take(5)->get();

        return $this->successResponse([
            'financial' => [
                'company_balance'       => $companyBalance,
                'total_paid_invoices'   => $totalPaidAmount,
                'paid_invoice_count'    => $paidInvoiceCount,
                'total_invoice_amount'  => $totalInvoiceAmount,
                'total_invoice_count'   => $totalInvoiceCount,
                'total_salaries_paid'   => $totalSalariesPaid,
                'pipeline_value'        => $pipelineValue,
            ],
            'projects' => [
                'total'     => $totalProjects,
                'ongoing'   => $ongoingProjects,
                'completed' => $completedProjects,
                'pending'   => $pendingProjects,
                'active'    => $activeProjects,
            ],
            'leads' => [
                'total'           => $totalLeads,
                'new'             => $newLeads,
                'won'             => $wonLeads,
                'conversion_rate' => $conversionRate,
                'pipeline'        => $pipelineByStatus,
                'monthly_chart'   => $monthlyLeads,
            ],
            'counts' => [
                'clients'     => $totalClients,
                'active_clients' => $activeClients,
                'services'    => $totalServices,
                'portfolios'  => $totalPortfolios,
                'blogs'       => $totalBlogs,
                'pending_quotations'  => $pendingQuotations,
                'accepted_quotations' => $acceptedQuotations,
            ],
            'recents' => [
                'transactions' => $recentTransactions,
                'leads'        => $recentLeads,
                'projects'     => $recentProjects,
            ]
        ], 'Dashboard metrics retrieved successfully.');
    }
}
