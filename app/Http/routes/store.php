<?php
//门店功能配置处
Route::group(['domain' => env('STORE_DOMAIN')],function(){
    // 认证路由...
    Route::get('/auth/login', 'Auth\StoreAuthController@getLogin');
    Route::post('/auth/login', 'Auth\StoreAuthController@postLogin');
    Route::get('/auth/logout', 'Auth\StoreAuthController@getLogout');
    Route::get('/auth/forget', 'Auth\StoreAuthController@getForget');
    Route::post('/auth/forget', 'Auth\StoreAuthController@postForget');
    Route::get('/auth/sms', 'Auth\StoreAuthController@sms');
});

Route::group(['domain' => env('STORE_DOMAIN'), 'namespace' => 'Store','middleware' => ['auth_store']], function () {

    Route::get('/', 'DashboardController@index');
    Route::get('/error/403', function() {return view('errors.403');});
    Route::get('/error/404', function() {return view('errors.404');});

    Route::get('/goods/audit', 'GoodsAuditController@index');
    Route::get('/goods/audit/{state}', 'GoodsAuditController@reviewGood');
    Route::get('/goods/manager', 'GoodsManagerController@index');

    Route::get('/goods/manager/{state}', 'GoodsManagerController@managerGood');
    Route::post('/goods/managers/{action}/{id}', 'GoodsManagerController@doGoods');
    Route::post('/goods/manager', 'GoodsManagerController@searchGood');



    //goods
    Route::get('/goods/review/{state?}','GoodsController@review');
    Route::get('/goods/lib/{status?}','GoodsController@index');
    Route::get('/goods/ajaxEdit/{id}/{action}','GoodsController@ajaxEdit');
    //审核菜单->编辑->查看
    Route::get('goods/{id}/review','GoodsController@reviewEdit');
    Route::get('goods/{id}/show','GoodsController@reviewShow');
    Route::post('goods/{id}/edit','GoodsController@update');
    Route::resource('/goods','GoodsController');
    //Route::get('/review/goods/{state?}','GoodsController@review');

    Route::post('/goods/upload','GoodsController@upload');
    Route::get('/gift/add','GoodsController@gift');
    Route::post('/gift/add','GoodsController@gift_store');
    Route::get('/gift/specs/{id}','GoodsController@goodsSpecs');
    Route::get('/gift/spec/{id}','GoodsController@goodsSpec');
    Route::get('/spec/add',function(){
        return view('store.goods.spec');
    });
    Route::post('/spec/add','GoodsController@spec');

    //supplier 供应商管理
    Route::get('/supplier/set','SupplierController@set');
    Route::post('/supplier/setting','SupplierController@setting');
    Route::post('/supplier/AlertSetting','SupplierController@AlertSetting');
    Route::get('/supplier/getCity/{province}','SupplierController@getCity');
    Route::post('/supplier/upload','SupplierController@upload');
    Route::get('/supplier/sendsms','SupplierController@send');
    Route::get('/supplier/password','SupplierController@getPassword');
    Route::post('/supplier/password','SupplierController@postPassword');
    Route::post('/supplier/uploadPlupLoad','SupplierController@uploadPlupLoad');
    
    //material 商品素材
    Route::get('/material/goodmaterial','SaleManageController@goodmaterial');
    Route::post('/material/uploadPlupLoad','SaleManageController@uploadPlupLoad');
    Route::post('/material/addMaterial','SaleManageController@addMaterial');
    //Route::post('/material/delmaterial','SaleManageController@delmaterial');
    Route::get('/material/delmaterial/{id}','SaleManageController@delmaterial');
    //carriage 邮费
    Route::get('/supplierExpress','SaleManageController@supplierExpressList');
    Route::post('/supplierExpress','SaleManageController@supplierExpressList');
    Route::post('/supplierExpress/edit','ExpressManageController@supplierExpressEdit');

    //order 订单管理
    Route::get('/order/deliverys/{state?}','OrderController@deliverys');
    Route::get('/order/delivery/{id}','OrderController@getDelivery');
    Route::post('/order/delivery','OrderController@orderManySend');//自提批量发货
    Route::get('/order/import','OrderController@getImport');
    Route::post('/order/import','OrderController@postImport');
    Route::post('/order/importexcel/','OrderController@importExcel');
    Route::get('/order/aftersales/{state?}','OrderController@afterSales');
    Route::get('/order/aftersale/{id}','OrderController@afterSale');
    Route::get('/order/aftersale/update/{id}','OrderController@afterSaleUpdate');
    Route::get('/order/all/{state?}','OrderController@all');
    Route::get('/order/show/{id}','OrderController@show');
    Route::post('/order/show','OrderController@showList');
    Route::post('/order/addexpress/{id}/{state}','OrderController@addexpress');
    Route::get('/order/export/{state?}','OrderController@export');
    Route::post('/order/delivery/{id}','OrderController@postDelivery');
    
    //comment 我的评价
    Route::get('/comment/index','CommentController@index');
    Route::get('/comment/detail/{orderno}','CommentController@detail');
    Route::post('/comment/reply','CommentController@commentReply');
    
    Route::get('/comment/getcomment/{id}','CommentController@getComment');
    
    //fund 资金管理
    Route::get('/fund','FundController@index');
    Route::get('/fund/record/{id?}','FundController@record');
    Route::get('/fund/record/export/{id}','FundController@export');
    Route::get('/fund/show/{id}','FundController@show');
    Route::get('/fund/withdraw','FundController@withdraw');
    Route::get('/fund/apply','FundController@apply');

    Route::post('/fund/apply','FundController@postApply');
    Route::post('/fund/withdraw','FundController@withdrawStore');


    Route::get('supplier/withdraw','SupplierController@supplierWithdraw');
    Route::post('/supplier/withdraw','SupplierController@withdraw');

});