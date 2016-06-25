<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', 'HomeController@index');
Route::get('/item/list', 'HomeController@itemList');
Route::get('/history/reslult', 'HomeController@getHistoryItems');
Route::get('/detail/{id}', 'HomeController@itemDetail');
Route::get('/test', 'HomeController@test');
Route::get('/detail/svn/detail/{id}', 'HomeController@getSvnDiff');


//轮流依次
Route::get('/hosts/deploy/{step}', 'HomeController@deployStep');
Route::get('/hosts/deploy/jobresult/{step}', 'HomeController@stepResult');
Route::post('/history/result/detail/{id}', 'HomeController@postHistoryResult');
Route::post('/modify/resultstage/detail/{id}', 'HomeController@modifyResultStage');

//修改并发
Route::get('/hosts/deployhosts', 'HomeController@deployHosts');
Route::get('/hosts/deployresult/hostresult/{hostnum}', 'HomeController@hostResult');
Route::get('/hosts/deployjoblast', 'HomeController@deployJobLast');
Route::get('/hosts/jobLast/result', 'HomeController@jobLastResult');
Route::get('/hosts/createhistory/result/detail/{id}', 'HomeController@createHistoryResult');
Route::get('/hosts/modify/resultstage/detail/{id}', 'HomeController@updateResultStage');

//修改后台操作添加类 重构代码
Route::get('/', 'AutoBuildController@index');
Route::get('/auto/item/list', 'AutoBuildController@itemList');
Route::get('/auto/detail/{id}', 'AutoBuildController@itemDetail');
Route::get('/auto/detail/svn/auto/detail/{id}', 'AutoBuildController@getSvnDiff');
Route::get('/auto/deploy/', 'AutoBuildController@autoDeploy');
Route::get('/auto/lasthistoryDBId/', 'AutoBuildController@getLastHistoryDBId');
Route::get('/auto/getresultstatus/', 'AutoBuildController@getResultStatus');
Route::get('/auto/history/reslult', 'AutoBuildController@getHistoryItems');
Route::get('/auto/dashboard', 'AutoBuildController@displayDashboard');

