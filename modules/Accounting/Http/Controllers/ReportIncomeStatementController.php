<?php

namespace Modules\Accounting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * Class ReportIncomeStatementController
 * Reporte de Estado de Resultados
 */
class ReportIncomeStatementController extends Controller
{
    public function records(Request $request)
    {
        $accounts = ChartOfAccount::whereIn('type', ['Revenue', 'Expense'])
            ->with(['journalEntryDetails' => function ($query) {
                $query->selectRaw('chart_of_account_id, SUM(debit) as total_debit, SUM(credit) as total_credit')
                      ->groupBy('chart_of_account_id');
            }])
            ->get()
            ->map(function ($account) {
                $debit = $account->journalEntryDetails->sum('total_debit');
                $credit = $account->journalEntryDetails->sum('total_credit');
                $saldo = $account->type === 'Revenue'
                    ? $credit - $debit
                    : $debit - $credit;

                return [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'saldo' => $saldo,
                ];
            });

        $totalRevenue = $accounts->where('type', 'Revenue')->sum('saldo');
        $totalExpense = $accounts->where('type', 'Expense')->sum('saldo');
        $netResult = $totalRevenue - $totalExpense;

        return response()->json([
            'accounts' => $accounts,
            'net_result' => $netResult,
        ]);
    }
}
