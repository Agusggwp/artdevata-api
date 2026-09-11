<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\SalaryPayment;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalaryController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $devRoles = ['Developer','Frontend','Backend','Fullstack','DevOps'];
        $qaRoles  = ['QA','Quality Assurance'];

        $projects = Project::where('status','completed')->with('team')->get();

        $perProject = [];
        $perAdminTotals = [];

        foreach ($projects as $project) {
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
                    $qaDetails[] = ['id' => $qa->id, 'name' => $qa->name, 'role' => $qa->pivot->role, 'amount' => $perQa];
                    $perAdminTotals[$qa->id] = ($perAdminTotals[$qa->id] ?? 0) + $perQa;
                }
            }

            if ($devs->count() > 0) {
                $perDev = round($devShare / $devs->count(), 2);
                foreach ($devs as $dev) {
                    $devDetails[] = ['id' => $dev->id, 'name' => $dev->name, 'role' => $dev->pivot->role, 'amount' => $perDev];
                    $perAdminTotals[$dev->id] = ($perAdminTotals[$dev->id] ?? 0) + $perDev;
                }
            }

            $perProject[] = [
                'id'          => $project->id,
                'name'        => $project->name,
                'budget'      => $budget,
                'qa_share'    => $qaShare,
                'dev_share'   => $devShare,
                'qa_details'  => $qaDetails,
                'dev_details' => $devDetails,
            ];
        }

        $payments = SalaryPayment::with('admin:id,name', 'project:id,name')->get();
        $totalPaidInvoices = (float) Invoice::where('status','paid')->sum('total');
        $totalSalariesPaid = (float) SalaryPayment::where('status','paid')->sum('amount');
        $companyBalance = round($totalPaidInvoices - $totalSalariesPaid, 2);

        return $this->successResponse([
            'company_balance'     => $companyBalance,
            'total_paid_invoices' => $totalPaidInvoices,
            'total_salaries_paid' => $totalSalariesPaid,
            'projects_payroll'    => $perProject,
            'admin_totals'        => $perAdminTotals,
            'payments_history'    => $payments,
        ], 'Data kompensasi & gaji berhasil diambil.');
    }

    public function pay(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'admin_id'   => 'required|exists:admins,id',
            'amount'     => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $payment = DB::transaction(function () use ($request) {
            $payment = SalaryPayment::updateOrCreate(
                ['project_id' => $request->project_id, 'admin_id' => $request->admin_id],
                ['amount' => $request->amount, 'status' => 'paid', 'paid_at' => now()]
            );

            AuditLogger::log(
                action: 'create',
                module: 'Finance',
                recordId: (string) $payment->id,
                description: "Mencatat pembayaran gaji (API) Rp" . number_format($payment->amount, 0, ',', '.') . " ke Admin #{$payment->admin_id}",
                newData: ['amount' => $payment->amount, 'project_id' => $payment->project_id]
            );

            return $payment;
        });

        return $this->successResponse($payment->load('admin:id,name', 'project:id,name'), 'Pembayaran gaji berhasil dicatat.', 201);
    }
}
