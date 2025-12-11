<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Blog;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Project;
use App\Models\Admin;
use App\Models\CompanyTransaction;
use App\Models\SalaryPayment;
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

        // Project counts
        $totalProjects = Project::count();
        $ongoingProjects = Project::where('status', 'ongoing')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $pendingProjects = Project::where('status', 'pending')->count();

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

        // Roles mapping (sesuaikan jika perlu)
        $devRoles = ['Developer','Frontend','Backend','Fullstack','DevOps'];
        $qaRoles  = ['QA','Quality Assurance'];

        // Ambil proyek yang selesai beserta timnya
        $completedProjectsList = Project::where('status', 'completed')->with('team')->get();

        foreach ($completedProjectsList as $project) {
            $budget = (float) $project->budget;

            // Hitung shares
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

        // Ambil data admin penerima (jika ada)
        if (!empty($devPayouts)) {
            $devRecipients = Admin::whereIn('id', array_keys($devPayouts))->get()->keyBy('id');
        }
        if (!empty($qaPayouts)) {
            $qaRecipients = Admin::whereIn('id', array_keys($qaPayouts))->get()->keyBy('id');
        }

        // hitung net transaksi manual (credit minus debit)
        $transactionsNet = (float) CompanyTransaction::selectRaw(
            "COALESCE(SUM(CASE WHEN type='credit' THEN amount WHEN type='debit' THEN -amount ELSE 0 END),0) as net"
        )->value('net');

        // bila Anda juga pakai companyBalance dari invoices/salaries, tambahkan di sini.
        // contoh: $companyBalance = $transactionsNet + (totalPaidInvoices - totalSalariesPaid)
        $totalPaidInvoices = \App\Models\Invoice::where('status','paid')->sum('total');
        $totalSalariesPaid = \App\Models\SalaryPayment::where('status','paid')->sum('amount');
        $companyBalance = round($totalPaidInvoices - $totalSalariesPaid + $transactionsNet, 2);

        $recentTransactions = CompanyTransaction::with('admin')->latest()->take(8)->get();

        return view('admin.panel', array_merge(compact(
            'totalPaidAmount','paidInvoiceCount','totalInvoiceAmount','totalInvoiceCount',
            'totalBlogs','totalPortfolios','totalServices',
            'totalProjects','ongoingProjects','completedProjects','pendingProjects',
            // payroll
            'totalQAPayout','totalDevPayout','qaPayouts','devPayouts','qaRecipients','devRecipients',
            'unassignedQAPool','unassignedDevPool',
            'projectPayrolls','completedProjectsList'
        ), [
            // tambahan variabel baru
            'companyBalance' => $companyBalance,
            'recentTransactions' => $recentTransactions,
        ]));
    }

    // ...existing methods yang sudah ada...
}