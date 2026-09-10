<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Blog;
use App\Models\Client;
use App\Models\CompanyTransaction;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SalaryPayment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    public function index()
    {
        // Invoice data
        $totalPaidAmount = Invoice::where('status', 'paid')->sum('total');
        $paidInvoiceCount = Invoice::where('status', 'paid')->count();
        $totalInvoiceAmount = Invoice::sum('total');
        $totalInvoiceCount = Invoice::count();

        // Blog, Portfolio, Services data
        $totalBlogs = Blog::count();
        $totalPortfolios = Portfolio::count();
        $totalServices = Service::count();
        $totalClients = Client::count();

        // Project counts
        $totalProjects = Project::count();
        $ongoingProjects = Project::whereIn('status', ['ongoing', 'in_progress', 'planning'])->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $pendingProjects = Project::whereIn('status', ['pending', 'on_hold', 'review'])->count();

        // Phase 2 Business Management Metrics
        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'new')->count();
        $wonLeads = Lead::where('status', 'won')->count();
        $conversionRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0;

        $activeClients = Client::where('status', 'active')->count();
        $activeProjects = Project::whereIn('status', ['planning', 'in_progress', 'ongoing', 'review'])->count();

        $pendingQuotations = Quotation::whereIn('status', ['draft', 'sent', 'viewed'])->count();
        $acceptedQuotations = Quotation::where('status', 'accepted')->count();

        // Pipeline Value (Sum of estimated budget of open leads + total of active quotations)
        $leadsPipeline = (float) Lead::whereIn('status', ['new', 'contacted', 'qualified', 'negotiation'])->sum('estimated_budget');
        $quotationsPipeline = (float) Quotation::whereIn('status', ['draft', 'sent', 'viewed'])->sum('total');
        $pipelineValue = $leadsPipeline + $quotationsPipeline;

        // Pipeline leads by status
        $pipelineByStatus = [
            'new'         => Lead::where('status', 'new')->count(),
            'contacted'   => Lead::where('status', 'contacted')->count(),
            'qualified'   => Lead::where('status', 'qualified')->count(),
            'negotiation' => Lead::where('status', 'negotiation')->count(),
            'won'         => Lead::where('status', 'won')->count(),
            'lost'        => Lead::where('status', 'lost')->count(),
        ];

        // Monthly Leads Chart Data (Last 6 Months)
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

        // --- Initialize payroll variables to avoid undefined errors ---
        $totalQAPayout = 0.0;
        $totalDevPayout = 0.0;
        $unassignedQAPool = 0.0;
        $unassignedDevPool = 0.0;
        $devPayouts = [];
        $qaPayouts = [];
        $devRecipients = collect();
        $qaRecipients = collect();
        $projectPayrolls = [];

        $devRoles = ['Developer', 'Frontend', 'Backend', 'Fullstack', 'DevOps'];
        $qaRoles  = ['QA', 'Quality Assurance'];

        $completedProjectsList = Project::where('status', 'completed')->with('team')->get();

        foreach ($completedProjectsList as $project) {
            $budget = (float) $project->budget;

            $qaShare = round($budget * 0.05, 2);
            $devShare = round($budget * 0.25, 2);

            $qas = $project->team->filter(fn($m) => in_array($m->pivot->role, $qaRoles));
            $devs = $project->team->filter(fn($m) => in_array($m->pivot->role, $devRoles));

            $qaDetails = [];
            $devDetails = [];

            if ($qas->count() > 0) {
                $perQa = round($qaShare / $qas->count(), 2);
                foreach ($qas as $qa) {
                    $qaPayouts[$qa->id] = ($qaPayouts[$qa->id] ?? 0) + $perQa;
                    $qaDetails[] = [
                        'admin_id' => $qa->id,
                        'name' => $qa->name,
                        'role' => $qa->pivot->role,
                        'amount' => $perQa
                    ];
                }
            } else {
                $unassignedQAPool += $qaShare;
            }
            $totalQAPayout += $qaShare;

            if ($devs->count() > 0) {
                $perDev = round($devShare / $devs->count(), 2);
                foreach ($devs as $dev) {
                    $devPayouts[$dev->id] = ($devPayouts[$dev->id] ?? 0) + $perDev;
                    $devDetails[] = [
                        'admin_id' => $dev->id,
                        'name' => $dev->name,
                        'role' => $dev->pivot->role,
                        'amount' => $perDev
                    ];
                }
            } else {
                $unassignedDevPool += $devShare;
            }
            $totalDevPayout += $devShare;

            $projectPayrolls[] = [
                'project_id' => $project->id,
                'name' => $project->name,
                'budget' => $budget,
                'qa_share' => $qaShare,
                'dev_share' => $devShare,
                'qa_details' => $qaDetails,
                'dev_details' => $devDetails,
                'unassigned_qa' => $qas->count() ? 0 : $qaShare,
                'unassigned_dev' => $devs->count() ? 0 : $devShare,
            ];
        }

        if (!empty($devPayouts)) {
            $devRecipients = Admin::whereIn('id', array_keys($devPayouts))->get()->keyBy('id');
        }
        if (!empty($qaPayouts)) {
            $qaRecipients = Admin::whereIn('id', array_keys($qaPayouts))->get()->keyBy('id');
        }

        $transactionsNet = (float) CompanyTransaction::selectRaw(
            "COALESCE(SUM(CASE WHEN type='credit' THEN amount WHEN type='debit' THEN -amount ELSE 0 END),0) as net"
        )->value('net');

        $totalPaidInvoices = Invoice::where('status', 'paid')->sum('total');
        $totalSalariesPaid = SalaryPayment::where('status', 'paid')->sum('amount');
        $companyBalance = round($totalPaidInvoices - $totalSalariesPaid + $transactionsNet, 2);

        $recentTransactions = CompanyTransaction::with('admin')->latest()->take(8)->get();

        return view('admin.panel', array_merge(compact(
            'totalPaidAmount', 'paidInvoiceCount', 'totalInvoiceAmount', 'totalInvoiceCount',
            'totalBlogs', 'totalPortfolios', 'totalServices', 'totalClients',
            'totalProjects', 'ongoingProjects', 'completedProjects', 'pendingProjects',
            'totalLeads', 'newLeads', 'conversionRate', 'activeClients', 'activeProjects',
            'pendingQuotations', 'acceptedQuotations', 'pipelineValue', 'pipelineByStatus', 'monthlyLeads',
            'totalQAPayout', 'totalDevPayout', 'qaPayouts', 'devPayouts', 'qaRecipients', 'devRecipients',
            'unassignedQAPool', 'unassignedDevPool',
            'projectPayrolls', 'completedProjectsList'
        ), [
            'companyBalance' => $companyBalance,
            'recentTransactions' => $recentTransactions,
        ]));
    }
}