<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Admin;
use App\Models\SalaryPayment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $devRoles = ['Developer','Frontend','Backend','Fullstack','DevOps'];
        $qaRoles  = ['QA','Quality Assurance'];

        $projects = Project::where('status','completed')->with('team')->get();

        $perProject = [];
        $perAdminTotals = [];

        foreach ($projects as $project) {
            $budget = (float)$project->budget;
            $qaShare = round($budget * 0.05, 2);
            $devShare = round($budget * 0.25, 2);

            $qas = $project->team->filter(fn($m) => in_array($m->pivot->role, $qaRoles));
            $devs = $project->team->filter(fn($m) => in_array($m->pivot->role, $devRoles));

            $qaDetails = []; $devDetails = [];

            if ($qas->count() > 0) {
                $perQa = round($qaShare / $qas->count(), 2);
                foreach ($qas as $qa) {
                    $qaDetails[] = ['id'=>$qa->id,'name'=>$qa->name,'role'=>$qa->pivot->role,'amount'=>$perQa];
                    $perAdminTotals[$qa->id] = ($perAdminTotals[$qa->id] ?? 0) + $perQa;
                }
            }

            if ($devs->count() > 0) {
                $perDev = round($devShare / $devs->count(), 2);
                foreach ($devs as $dev) {
                    $devDetails[] = ['id'=>$dev->id,'name'=>$dev->name,'role'=>$dev->pivot->role,'amount'=>$perDev];
                    $perAdminTotals[$dev->id] = ($perAdminTotals[$dev->id] ?? 0) + $perDev;
                }
            }

            $perProject[] = [
                'id'=>$project->id,
                'name'=>$project->name,
                'budget'=>$budget,
                'qa_share'=>$qaShare,
                'dev_share'=>$devShare,
                'qa_details'=>$qaDetails,
                'dev_details'=>$devDetails,
            ];
        }

        // ambil pembayaran existing untuk menandai paid/unpaid
        $payments = SalaryPayment::whereIn('project_id', $projects->pluck('id'))->get()
            ->keyBy(fn($p) => $p->project_id.'_'.$p->admin_id);

        // total kas perusahaan = total invoice paid - total salary paid
        $totalPaidInvoices = Invoice::where('status','paid')->sum('total');
        $totalSalariesPaid = SalaryPayment::where('status','paid')->sum('amount');
        $companyBalance = round($totalPaidInvoices - $totalSalariesPaid, 2);

        $admins = !empty($perAdminTotals) ? Admin::whereIn('id', array_keys($perAdminTotals))->get()->keyBy('id') : collect();

        return view('admin.salaries.index', compact('perProject','perAdminTotals','admins','payments','companyBalance'));
    }

    public function pay(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'admin_id' => 'required|exists:admins,id',
            'amount' => 'required|numeric|min:0.01'
        ]);

        // jika sudah ada entry, update status menjadi paid
        $payment = SalaryPayment::updateOrCreate(
            ['project_id'=>$request->project_id,'admin_id'=>$request->admin_id],
            ['amount'=>$request->amount,'status'=>'paid','paid_at'=>now()]
        );

        return redirect()->route('admin.salaries.index')->with('success','Pembayaran tercatat.');
    }
}