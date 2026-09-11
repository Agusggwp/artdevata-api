<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyTransaction;
use App\Models\Invoice;
use App\Models\SalaryPayment;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FinanceController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $qType = $request->query('type');
        $qFrom = $request->query('from');
        $qTo   = $request->query('to');
        $qSearch = $request->query('search');

        $query = CompanyTransaction::with('admin:id,name')->orderByDesc('created_at');

        if ($qType && in_array($qType, ['credit', 'debit'])) {
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

        $perPage = $request->get('per_page', 20);
        $transactions = $query->paginate($perPage);

        $transactionsNet = (float) CompanyTransaction::selectRaw(
            "COALESCE(SUM(CASE WHEN type='credit' THEN amount WHEN type='debit' THEN -amount ELSE 0 END),0) as net"
        )->value('net');

        $totalPaidInvoices = (float) Invoice::where('status','paid')->sum('total');
        $totalSalariesPaid = (float) SalaryPayment::where('status','paid')->sum('amount');
        $companyBalance = round($totalPaidInvoices - $totalSalariesPaid + $transactionsNet, 2);

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi keuangan berhasil diambil.',
            'company_balance' => $companyBalance,
            'data'            => $transactions->items(),
            'meta'            => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type'        => 'required|in:credit,debit',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $tx = DB::transaction(function () use ($request) {
            $netManual = (float) CompanyTransaction::selectRaw(
                "COALESCE(SUM(CASE WHEN type='credit' THEN amount WHEN type='debit' THEN -amount ELSE 0 END),0) as net"
            )->value('net');

            $newBalance = $netManual + ($request->type === 'credit' ? (float) $request->amount : -(float) $request->amount);

            $transaction = CompanyTransaction::create([
                'admin_id'      => $request->user()?->id,
                'type'          => $request->type,
                'amount'        => $request->amount,
                'description'   => $request->description,
                'balance_after' => $newBalance,
            ]);

            AuditLogger::log(
                action: 'create',
                module: 'Finance',
                recordId: (string) $transaction->id,
                description: "Mencatat transaksi keuangan (API) {$transaction->type} Rp" . number_format($transaction->amount, 0, ',', '.'),
                newData: ['type' => $transaction->type, 'amount' => $transaction->amount]
            );

            return $transaction;
        });

        return $this->successResponse($tx->load('admin:id,name'), 'Transaksi keuangan berhasil dicatat.', 201);
    }
}
