<?php

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

Route::get('/', function () {
    return view('welcome');
});
Route::group([
    'prefix'        => 'reports',
], function () {
    Route::get('client_quotations', 'ReportController@client_quotations');
    Route::get('sales', 'ReportController@sales');
    Route::get('propoals_graphs', 'ReportController@propoals_graphs');
    Route::get('effectiveness', 'ReportController@effectiveness');

});
Route::group([
    'prefix'         =>  'dashboard'
], function(){
    Route::get('tasks_opens','DashboardController@tasksOpens');
    Route::get('approved_sales_month','DashboardController@approvedSalesMonth');
    Route::get('graph_sales_year','DashboardController@graphSalesYear');
    Route::get('propoals_graphs', 'DashboardController@propoals_graphs');
    Route::get('goals_graphs', 'DashboardController@goals_graphs');

});
Route::get('quotations/pdf/{id}', 'QuotationController@pdf');
Route::get('payments/export/{id}','PaymentController@export');
Route::get('passenger/export/{id}','PassengerController@export');
Route::get('quotations/balance/{id}','QuotationController@balance');
