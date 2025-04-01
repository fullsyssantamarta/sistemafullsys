<?php

namespace Modules\Accounting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * Class ReportFinancialPositionController
 * Reporte de Situación Financiera
 */
class ReportFinancialPositionController extends Controller
{
    public function records(Request $request)
    {
        $accounts = ChartOfAccount::whereIn('type', ['Asset', 'Liability', 'Equity'])
            ->with(['journalEntryDetails' => function ($query) {
                $query->selectRaw('chart_of_account_id, SUM(debit) as total_debit, SUM(credit) as total_credit')
                      ->groupBy('chart_of_account_id');
            }])
            ->get()
            ->map(function ($account) {
                $debit = $account->journalEntryDetails->sum('total_debit');
                $credit = $account->journalEntryDetails->sum('total_credit');
                $saldo = $debit - $credit;

                return [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'saldo' => $saldo,
                ];
            });

        return response()->json($accounts);
    }
}
