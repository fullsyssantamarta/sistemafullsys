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
            Route::apiResource('charts', 'ChartOfAccountController');

            // CRUD para Prefijos de Asientos Contables
            Route::apiResource('journal/prefixes', 'JournalPrefixController');

            // CRUD para Asientos Contables
            Route::get('journal/entries/records', 'JournalEntryController@records');
            Route::apiResource('journal/entries', 'JournalEntryController')->names([
                'index'   => 'tenant.accounting.journal.entries.index',
            ]);
            Route::put('journal/entries/{id}/request-approval', 'JournalEntryController@requestApproval');
            Route::put('journal/entries/{id}/approve', 'JournalEntryController@approve');
            Route::put('journal/entries/{id}/reject', 'JournalEntryController@reject');

            // CRUD para Detalles de Asientos Contables
            Route::apiResource('journal/entry-details', 'JournalEntryDetailController');
        });
    });
}