<?php

namespace Modules\Accounting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/*
 * Class JournalEntryDetailController
 * Controlador para gestionar los detalles de los asientos contables
 */
class JournalEntryDetailController extends Controller
{
    public function index()
    {
        return JournalEntryDetail::with('entry', 'account')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'journal_entry_id' => 'required|exists:journal_entries,id',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'debit' => 'required|numeric|min:0',
            'credit' => 'required|numeric|min:0',
        ]);

        return JournalEntryDetail::create($request->all());
    }

    public function show($id)
    {
        return JournalEntryDetail::with('entry', 'account')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $detail = JournalEntryDetail::findOrFail($id);
        $detail->update($request->all());
        return $detail;
    }

    public function destroy($id)
    {
        $detail = JournalEntryDetail::findOrFail($id);
        $detail->delete();
        return response()->json(['message' => 'Eliminado correctamente']);
    }
}
