<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        Route::prefix('accounting')->middleware(['auth'])->group(function() {
            Route::get('/', 'AccountingController@index');
            Route::get('/columns', 'AccountingController@columns');

            // CRUD para Cuentas Contables
            Route::get('charts/records', 'ChartOfAccountController@records');
            Route::get('charts/columns', 'ChartOfAccountController@columns');
            Route::get('charts/children/{parent_id}', 'ChartOfAccountController@getChildren');
            Route::get('charts/parent/{parent_id}', 'ChartOfAccountController@getChildren');
            Route::apiResource('charts', 'ChartOfAccountController')->names([
                'index'   => 'tenant.accounting.charts.index',
            ]);

            // CRUD para Prefijos de Asientos Contables
            Route::apiResource('journal/prefixes', 'JournalPrefixController');

            // CRUD para Asientos Contables
            Route::get('journal/entries/columns', 'JournalEntryController@columns');
            Route::get('journal/entries/records', 'JournalEntryController@records');
            Route::apiResource('journal/entries', 'JournalEntryController')->names([
                'index'   => 'tenant.accounting.journal.entries.index',
            ]);
            Route::put('journal/entries/{id}/request-approval', 'JournalEntryController@requestApproval');
            Route::put('journal/entries/{id}/approve', 'JournalEntryController@approve');
            Route::put('journal/entries/{id}/reject', 'JournalEntryController@reject');

            // CRUD para Detalles de Asientos Contables
            Route::apiResource('journal/entry-details', 'JournalEntryDetailController');
            Route::get('journal/entries/{id}/records-detail', 'JournalEntryDetailController@recordsDetail');

            // Reportes
            // Reporte de Situación Financiera
            Route::get('/financial-position', 'FinancialPositionController@index')->name('tenant.accounting.report.financial-position');
            Route::get('/financial-position/records', 'FinancialPositionController@records');
            // Reporte de Estado de Resultados
            Route::get('/income-statement', 'IncomeStatementController@index')->name('tenant.accounting.report.income-statement');
            Route::get('/income-statement/records', 'IncomeStatementController@records');

        });
    });
}