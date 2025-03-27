<?php

namespace Modules\Accounting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Accounting\Models\ChartOfAccount;

class ChartOfAccountController extends Controller
{
    /**
     * Retrieve all accounts
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ChartOfAccount::with('parent');
        if ($request->has('level')) {
            $query->where('level', $request->input('level'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }


    /**
     * Create a new account
     *
     * @bodyParam code string required Code of account. Example: 1101
     * @bodyParam name string required Name of account. Example: Bank
     * @bodyParam type string required Required type of account. Example: Asset
     * @bodyParam parent_id integer nullable Optional parent account id. Example: 1
     * @bodyParam level integer required Required level of account. Example: 1
     * @bodyParam status boolean required Required status of account. Example: 1
     * @response 201 {
     *  "data": {
     *      "id": 1,
     *      "code": "1101",
     *      "name": "Bank",
     *      "type": "Asset",
     *      "parent_id": 1,
     *      "level": 1,
     *      "status": 1,
     *      "created_at": "2020-09-01 00:00:00",
     *      "updated_at": "2020-09-01 00:00:00"
     *  }
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:chart_of_accounts|max:8',
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense,Cost',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'level' => 'required|integer|min:1|max:4',
            'status' => 'boolean'
        ]);

        $account = ChartOfAccount::create($request->all());
        return response()->json($account, 201);
    }

    /**
     * Display the specified account with its children.
     *
     * @param  int  $id  The ID of the account to display.
     * @return \Illuminate\Http\JsonResponse  The JSON response containing the account details.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  If the account is not found.
     */

    public function show($id)
    {
        $account = ChartOfAccount::with('children')->findOrFail($id);
        return response()->json($account);
    }

    /**
     * Update the specified account.
     *
     * @bodyParam name string Name of account. Example: Bank
     * @bodyParam type string Required type of account. Example: Asset
     * @bodyParam parent_id integer Optional parent account id. Example: 1
     * @bodyParam level integer Required level of account. Example: 1
     * @bodyParam status boolean Required status of account. Example: 1
     * @response 200 {
     *  "data": {
     *      "id": 1,
     *      "code": "1101",
     *      "name": "Bank",
     *      "type": "Asset",
     *      "parent_id": 1,
     *      "level": 1,
     *      "status": 1,
     *      "created_at": "2020-09-01 00:00:00",
     *      "updated_at": "2020-09-01 00:00:00"
     *  }
     * }
     */
    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'type' => 'in:Asset,Liability,Equity,Revenue,Expense,Cost',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'level' => 'integer|min:1|max:4',
            'status' => 'boolean'
        ]);

        $account->update($request->all());
        return response()->json($account);
    }

    /**
     * Delete the specified account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $account->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}
