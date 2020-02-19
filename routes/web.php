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


Route::post('/user', 'User\UserController@store');
Route::get('/user/wallet', 'User\WalletController@getList');
Route::post('/user/login', 'User\AuthController@postLogin');
Route::post('/demo/user/login/{sequence}', 'User\AuthController@postDemoSpecifiedLogin');
Route::post('/demo/user/login', 'User\AuthController@postDemoLogin');
Route::get('/demo/user', 'User\AuthController@getListDemoUsers');

// Widget
Route::get('/mining/arcade','Mining\WidgetController@getArcadeBlock');
Route::get('/mining/portal','Mining\WidgetController@getPortalBlock');
//TODO: check with 'filters'
Route::get('/mining/rewards','Block\BlockController@getRewardsWidget');
Route::get('/mining/my/rewards','Block\BlockController@getMyRewardsWidget');
Route::get('/mining/arcade/hash','Mining\WidgetController@getHashBalance');

//Mining
Route::post('/mining/{asset}/deposit','Mining\TransactionController@postDeposit');
Route::post('/mining/{asset}/withdraw','Mining\TransactionController@postWithdraw');
Route::get('/mining/{asset}/predict','Mining\TransactionController@getHashratePrediction');
Route::get('/mining/{asset}/maxes','Mining\TransactionController@getMaxValues');
Route::get('/mining/stats','Dashboard\DashboardController@getOverviewStatistics');

// Transactions
Route::post('/transaction/free_deposit', 'Transaction\TransactionController@postFreeDeposit');
Route::post('/transaction/exchange/{currency}', 'Transaction\TransactionController@postExchange');

// Assets
Route::get('/asset', 'Asset\AssetController@getList');
Route::get('/asset/mineable', 'Asset\AssetController@getMineable');
Route::post('/user/asset/{asset}', 'Mining\WidgetController@postCreateAsset');

// Chart
Route::get('/mining/chart/{type}', 'Mining\ChartController@getChart');
Route::get('/mining/chart', 'Mining\ChartController@getChart');

// Admin
Route::get('/admin/restart', 'AdminController@getRestart');
Route::get('/admin/overview', 'AdminController@getOverview');
Route::post('/admin/overview', 'AdminController@postOverview');
