<?php
//旅行社认证
Route::group(['domain' => env('TRAVEL_DOMAIN')],function(){
    // 认证路由...
    Route::get('/auth/login', 'Auth\TravelAuthController@getLogin');
    Route::get('/auth/register', 'Auth\TravelAuthController@getRegister');
    Route::post('/auth/register', 'Auth\TravelAuthController@postRegister');
    Route::get('/auth/forget', 'Auth\TravelAuthController@getForget');
    Route::post('/auth/forget', 'Auth\TravelAuthController@postForget');
    Route::post('/auth/login', 'Auth\TravelAuthController@postLogin');
    Route::get('/auth/logout', 'Auth\TravelAuthController@getLogout');
    Route::get('/auth/sms', 'Auth\TravelAuthController@sms');
});
Route::group(['domain' => env('TRAVEL_DOMAIN'), 'namespace' => 'Travel','middleware' => ['auth_travel']], function () {
    Route::get('/', 'DashboardController@index');
    //首页面绑定人数路由
    Route::get('/dashboard/numAndSale/{flag}', 'DashboardController@numAndSale');
    Route::get('/dashboard/export/{state}','DashboardController@export');
    Route::get('/dashboard/info/{id}','DashboardController@getAllInfo');
    //Route::get('/dashboard/right','DashboardController@getRightAllInfo');
    //Route::get('/dashboard/totalSale/{flag?}', 'DashboardController@totalSale');
    
    Route::get('/manage/guides/{keywords?}','ManageController@guides');
    Route::post('/manage/guideStore','ManageController@guideStore');
    Route::post('/manage/modifyAlias','ManageController@modifyAlias');
    Route::post('/manage/setGuiderGroup','ManageController@setGuiderGroup');
    Route::get('/manage/guide/{id}','ManageController@guide');
    Route::get('/manage/export','ManageController@export');
    Route::get('/manage/exportGroup','ManageController@exportGroup');
    //Route::get('/manage/visitors/create','ManageController@visitors_add');
    Route::post('/manage/visitors/group','ManageController@visitors_group');
    Route::get('/manage/visitors/orders/{id}','ManageController@visitors_order');
    Route::get('/manage/visitors/groupOrdersDetail/{id}','ManageController@groupOrdersDetail');
    Route::get('/manage/visitors/{keywords?}','ManageController@visitors');
    Route::get('/manage/testupdate','ManageController@testupdate');
    
    Route::get('/fund/myincome','FundController@myincome');
    Route::get('/fund/incomes/{id?}','FundController@incomes');
    Route::get('/fund/income/{id}','FundController@income');
    Route::get('/fund/export/{id?}','FundController@export');
    Route::get('/fund/withdraws','FundController@withdraws');
    Route::get('/fund/apply','FundController@apply');
    Route::post('/fund/apply','FundController@postApply');
    Route::get('/fund/withdraw/{id}','FundController@withdraw');
    Route::post('/fund/withdraw','FundController@withdrawStore');

    Route::get('/system/set','SystemController@set');
    Route::get('/system/sendsms','SystemController@send');
    Route::post('/system/set','SystemController@setting');
    Route::post('/system/upload','SystemController@upload');
    Route::post('/system/uploads','SystemController@uploads');
    Route::get('/system/authentication','SystemController@authentication');
    Route::post('/system/authentication','SystemController@postAuthentication');
    Route::get('/system/authenticate','SystemController@authenticate');
    Route::get('/system/password','SystemController@password');
    Route::post('/system/password','SystemController@postPassword');
    Route::get('/system/getCity/{id}','SystemController@getCity');
    
    Route::get('/travel/getCity/{province}','TravelController@getCity');
    Route::post('/travel/AlertSetting','TravelController@AlertSetting');
    Route::post('/travel/uploadPlupLoad','TravelController@uploadPlupLoad');


});