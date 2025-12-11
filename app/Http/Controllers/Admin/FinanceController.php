<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyTransaction;
use App\Models\Invoice;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    // tampilkan riwayat transaksi (filterable, paginated)
    public function index(Request $request)
    {
        $qType = $request->query('type');
        $qFrom = $request->query('from');
        $qTo   = $request->query('to');
        $qSearch = $request->query('q');

        $query = CompanyTransaction::with('admin')->orderByDesc('created_at');

        if ($qType && in_array($qType, ['credit','debit'])) {
            $query->where('type', $qType);
        }
        if ($qFrom) {
            $query->whereDate('created_at', '>=', $qFrom);
        }
        if ($qTo) {
            $query->whereDate('created_at', '<=', $qTo);
        }
        if ($qSearch) {
            $query->where('description', 'like', "%{$qSearch}%");
        }

        $transactions = $query->paginate(20)->withQueryString();

        // current company balance
        $transactionsNet = (float) CompanyTransaction::selectRaw(
            "COALESCE(SUM(CASE WHEN type='credit' THEN amount WHEN type='debit' THEN -amount ELSE 0 END),0) as net"
        )->value('net');
        $totalPaidInvoices = (float) Invoice::where('status','paid')->sum('total');
        $totalSalariesPaid = (float) SalaryPayment::where('status','paid')->sum('amount');
        $companyBalance = round($totalPaidInvoices - $totalSalariesPaid + $transactionsNet, 2);

        // balance snapshot per transaction using status='paid' and updated_at <= transaction time
        $balanceMap = [];
        foreach ($transactions as $t) {
            $manualNet = (float) CompanyTransaction::selectRaw(
                "COALESCE(SUM(CASE WHEN type='credit' THEN amount WHEN type='debit' THEN -amount ELSE 0 END),0) as net"
            )->where('created_at', '<=', $t->created_at)->value('net');

            // gunakan status='paid' dan updated_at (bukan paid_at)
            $invoicesPaidUpTo = (float) Invoice::where('status','paid')
                ->where('updated_at', '<=', $t->created_at)
                ->sum('total');

            $salariesPaidUpTo = (float) SalaryPayment::where('status','paid')
                ->where(function($q) use ($t) {
                    // jika ada paid_at gunakan paid_at, kalau tidak fallback ke updated_at
                    $q->where('paid_at', '<=', $t->created_at)
                      ->orWhere(function($q2) use ($t) {
                          $q2->whereNull('paid_at')->where('updated_at', '<=', $t->created_at);
                      });
                })->sum('amount');

            $balanceAtTime = round($invoicesPaidUpTo - $salariesPaidUpTo + $manualNet, 2);
            $balanceMap[$t->id] = $balanceAtTime;
        }

        return view('admin.finance.transactions.index', compact(
            'transactions','companyBalance','qType','qFrom','qTo','qSearch','balanceMap'
        ));
    }

    // simpan penyesuaian saldo (tetap seperti sebelumnya)
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        // net saat ini (manual transactions)
        $netManual = (float) CompanyTransaction::selectRaw(
            "COALESCE(SUM(CASE WHEN type='credit' THEN amount WHEN type='debit' THEN -amount ELSE 0 END),0) as net"
        )->value('net');

        $newBalance = $netManual + ($request->type === 'credit' ? $request->amount : -$request->amount);

        $tx = CompanyTransaction::create([
            'admin_id' => Auth::guard('admin')->id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'balance_after' => $newBalance,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dicatat.');
    }
}