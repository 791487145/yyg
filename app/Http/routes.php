<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
include 'routes/travel.php';
include 'routes/store.php';
Route::group(['domain' => env('BOSS_DOMAIN')],function(){
    // 认证路由...
    Route::get('/auth/login', 'Auth\AuthController@getLogin');
    Route::post('/auth/login', 'Auth\AuthController@postLogin');
    Route::get('/auth/logout', 'Auth\AuthController@getLogout');
    //邮件审核
    Route::get('guide/{id}/mail/audit','Admin\CuscomerController@guideMailAudit');
});

Route::group(['domain' => env('BOSS_DOMAIN'), 'namespace' => 'Admin', 'middleware' => ['auth', 'authorize']], function () {
    //Route::get('/', 'DashboardController@welcome');
    Route::get('/', 'DashboardController@index');
    Route::get('/dashboard', 'DashboardController@dash');
    Route::get('/error/403', function() {return view('errors.403');});
    Route::get('/error/404', function() {return view('errors.404');});

    //perm 权限设置
    //role
    Route::get('/perm/role', 'RoleController@index');
    Route::get('/perm/role/create', 'RoleController@create');
    Route::post('/perm/role/create', 'RoleController@store');
    Route::get('/perm/role/{role}/update', 'RoleController@show');
    Route::post('/perm/role/{role}/update', 'RoleController@update');
    Route::post('/perm/role/{role}/delete', 'RoleController@destroy');
    Route::get('/perm/role/{name}/check', 'RoleController@checkRoleName');

    //user
    Route::get('/perm/user', 'UserController@index');
    Route::get('/perm/user/create', 'UserController@create');
    Route::post('/perm/user/create', 'UserController@store');
    Route::get('/perm/user/{user}/update', 'UserController@show');
    Route::post('/perm/user/{user}/update', 'UserController@update');
    Route::get('/perm/user/{user}/password', 'UserController@showPassword');
    Route::post('/perm/user/{user}/password', 'UserController@editPassword');
    Route::post('/perm/user/{user}/reset', 'UserController@resetPassword');
    Route::post('/perm/user/{user}/delete', 'UserController@destroy');

    //menu
    Route::get('/perm/menu', 'MenuController@index');
    Route::get('/perm/menu/create', 'MenuController@create');
    Route::post('/perm/menu/create', 'MenuController@store');
    Route::get('/perm/menu/{menu}/update', 'MenuController@show');
    Route::post('/perm/menu/{menu}/update', 'MenuController@update');
    Route::post('/perm/menu/{menu}/order', 'MenuController@displayOrder');
    Route::post('/perm/menu/{menu}/delete', 'MenuController@destroy');

    //permission
    Route::get('/perm/menu/{menu}/permission', 'PermissionController@index');
    Route::get('/perm/permission/create/{menu}', 'PermissionController@create');
    Route::post('/perm/permission/create/{menu}', 'PermissionController@store');
    Route::get('/perm/permission/{permission}/update', 'PermissionController@show');
    Route::post('/perm/permission/{permission}/update', 'PermissionController@update');
    Route::post('/perm/permission/{permission}/delete', 'PermissionController@destroy');

    //goods
    Route::get('/goods/index/{state?}','GoodsController@index');
    Route::get('/goods/check/{state?}','GoodsController@check');
    Route::post('/goods/index/{state?}','GoodsController@index');
    Route::get('/goods/export/{state?}','GoodsController@exportGoods');
    Route::any('/goods/action/{action}/{id}','GoodsController@action');
    Route::get('/goods/location_fix/{id}','GoodsController@location_fix');
    Route::post('/goods/location_fix','GoodsController@location_edit');
    Route::get('/goods/edit/{id}','GoodsController@edit');
    Route::post('/goods/edit/{id}','GoodsController@update');
    Route::get('/gift/add','GoodsController@gift');
    Route::get('/gift/guide/{id}','GoodsController@gift_guide');
    Route::post('/gift/add','GoodsController@gift_store');
    Route::get('/desc/add',function(){
        return view('boss.goods.desc');
    });
    Route::post('/desc/add','GoodsController@desc');
    Route::get('/location/','GoodsController@changeLocation');
    Route::get('/goods/show/{id}','GoodsController@show');
    Route::post('/goods/numsold/edit','GoodsController@goodsNunSoldEdit');
    Route::get('/goods/refute/{id}',function($id){
        return view('boss.goods.refute')->with('id',$id);
    });


    //upload
    Route::post('/goods/upload','GoodsController@upload');
    
    //comment 订单评价
    Route::get('comment/index','CommentController@index');
    Route::get('/comment/detail/{orderno}','CommentController@detail');
    Route::post('/comment/changestate','CommentController@changeState');

    //orders
    Route::get('/orders/list/','OrderController@QAGetOrders');//测试人员删除订单使用
    Route::post('/orders/delete/','OrderController@deleteOrder');//测试人员删除订单使用

    //refund
    Route::get('/orders/check/{state?}','OrderController@check');//审核
    Route::get('/orders/return/export/{state?}','OrderController@returnOrderExport');//审核
    Route::get('/orders/checkDetail/{order_no}/{authority?}','OrderController@checkDetail');
    Route::get('/orders/check/{action}/{order_no}/{amount_real}','OrderController@refundPass');
    Route::get('/orders/check/{order_no}/{action}','OrderController@refuseShow');//驳回退款
    Route::get('/orders/refund/{state?}','OrderController@refund');//退款
    Route::get('/orders/allorders/{state?}','OrderController@allOrders');//全部订单
    Route::get('/order/supplier/info/{order_no}','OrderController@getOrderSupplierInfo');//获取订单供应商信息
    Route::get('/orders/export/{state}','OrderController@export');
    Route::get('/orders/express/type/{action?}','OrderController@expressType');
    Route::get('/orders/ordersDetail/{order_no}','OrderController@ordersDetail');
    Route::post('/order/supplier/sms/{content}','OrderController@orderPressSms');//催单短信
    Route::post('/orders/detail/{order_no}','OrderController@updateExpressNumber');
    //auto refund
    Route::post('/orders/autoRefund/{orderNo}','OrderController@autoRefund');//自动退款

    Route::get('/orders/search/error/refund','OrderController@search');//搜索
    Route::get('/order/change/address/{order_no}','OrderController@changeAddress');//修改地址
    Route::post('/province/citys/{parent_id}','OrderController@getProvinceCitys');//省市区名称
    Route::post('/order/change/address/{order_no}','OrderController@handleChangeAddress');//修改地址
    //conf
    Route::get('/conf/confLunBo','ConfController@lunBolist');//；轮播图
    Route::get('/conf/confLunBo/add','ConfController@addLunBo');
    Route::post('/conf/confLunBo/add','ConfController@addingLunBo');
    Route::post('/conf/upload','ConfController@upload');
    Route::post('/conf/uploadMaterial','ConfController@uploadMaterial');
    Route::post('/conf/materialimgdel','ConfController@materialImgDel');

    Route::get('/conf/confLunBo/update/{id}','ConfController@updateLunBo');//修改
    Route::get('/conf/confLunBo/del/{id}','ConfController@delLunBo');
    Route::post('/conf/confLunBo/edit','ConfController@editLunBo');

    Route::get('/conf/confShop','ConfController@localShoplist');//地方馆
    Route::get('/conf/confShop/adding','ConfController@localShopadd');
    Route::post('/conf/confShop/adding','ConfController@addLocalShop');
    Route::get('/conf/confShop/del/{action}/{id}','ConfController@localShopdel');
    Route::get('/conf/confShop/update/{id}','ConfController@localShopUpdate');
    Route::post('/conf/confShop/update','ConfController@localShopEdit');

    Route::get('/conf/confKeywords','ConfController@keyWordlist');//关键字
    Route::get('/conf/confKeyword','ConfController@keyWordadd');//添加
    Route::post('/conf/confKeyword','ConfController@keyWordadd');
    Route::get('/conf/confKeyword/{id}','ConfController@delKeyWord');//删除
    Route::get('/conf/confKeywords/{id}','ConfController@keyWordEdit');//修改
    Route::post('/conf/confKeywords/{id}','ConfController@editKeyWord');

    Route::get('/conf/confCategorys','ConfController@categorylist');//商品品类
    Route::get('/conf/confCategory','ConfController@categoryAdd');//添加
    Route::post('/conf/confCategory','ConfController@categoryAdd');
    Route::get('/conf/confCategory/{id}','ConfController@delCategory');//删除
    Route::get('/conf/confCategorys/{id}','ConfController@categoryEdit');//修改
    Route::post('/conf/confCategorys/{id}','ConfController@editCategory');

    Route::get('/conf/confThemes','ConfController@themelist');//专题
    Route::get('/conf/confTheme','ConfController@themeAdd');
    Route::post('/conf/confTheme','ConfController@addTheme');
    Route::get('/conf/confTheme/{id}','ConfController@delTheme');
    Route::get('/conf/confThemes/{id}','ConfController@themeEdit');
    Route::post('/conf/confThemes/{id}','ConfController@editTheme');

    Route::get('/conf/materials','ConfController@materialList');//素材库
    Route::get('/conf/edit/{id}','ConfController@editMaterial');//编辑素材
    Route::post('/conf/update','ConfController@updateMaterial');//更新素材
    Route::post('/conf/material','ConfController@addMaterial');
    Route::get('/conf/materials/{id}','ConfController@materialDel');


    Route::get('/conf/confnotices','ConfController@platFormNotices');//平台公告
    Route::post('/conf/confnotice','ConfController@platFormNotice');//添加公告
    Route::get('/conf/confnotices/{id}','ConfController@updateNotice');//发送公告
    Route::get('/conf/confnotice/{id}','ConfController@deleteNotice');//删除公告
    Route::get('/conf/confImgnotices','ConfController@ImgNotice');//图文公告
    Route::get('/conf/confImgnotice','ConfController@addImgNotice');//添加图文公告
    Route::get('/conf/confImgnoticehandle','ConfController@handleaddimg');//处理图文公告

    Route::get('/express/index','ConfController@expressList');//快递公司列表
    Route::get('/expresses','ConfController@expressAdd');//快递公司添加
    Route::post('/expresses','ConfController@expressAdd');//快递公司添加
    Route::get('/express/{id}','ConfController@expressDel');//快递公司删除
    Route::get('/expresses/{id}','ConfController@expressEdit');//快递公司修改*/
    Route::post('/expresses/index','ConfController@editExpress');

    //coupon
    Route::get('/coupons/','CouponController@index');//优惠券
    Route::get('/coupons/add','CouponController@create');//添加优惠券
    Route::get('/coupons/destroy/{id}','CouponController@destroy');//保存优惠券
    Route::get('/coupons/edit/{id}','CouponController@edit');//编辑优惠券
    Route::get('/coupons/show/{id}/{state?}','CouponController@show');//查看优惠券
    Route::get('/coupons/used/count/{id}/{state}','CouponController@couponUseStateCount');//查看优惠券
    Route::get('/coupons/user/{uid}/{coupon_id}','CouponController@couponUser');//查看优惠券
    Route::get('/coupons/supplier/add/goods/{supplier_id}','CouponController@supplierCouponGoods');
    Route::post('/coupon/update/{id}','CouponController@update');//更新优惠券
    Route::post('/coupon/supplier/goods/{supplier_id}','CouponController@supplierAddCouponGoods');//供应商添加
    /*Route::post('/coupon/supplier/goods/{supplier_id}/{id?}','CouponController@supplierGoods');*/
    Route::post('/coupons/add','CouponController@store');//保存优惠券
    //Ta
    Route::get('/ta/tamanage/{id}','TaController@TAMmanageInfo');//旅行社信息
    Route::get('/ta/unaudited/{id}','TaController@TaUnauditedList');//旅行社信息
    Route::post('/ta/checkPass','TaController@checkPass');//审核
    Route::get('/ta/checkRefuse/{id}','TaController@checkRefuse');//驳回审核
    Route::get('/ta/guideauditlog/{id}','TaController@GuideAudioLog');//提交审核日志
    Route::any('/ta/tamanages','TaController@TAManages');//旅行社列表
    Route::get('/ta/add/','TaController@addTA');//添加旅行社
    Route::get('/ta/export/','TaController@exportTaLists');//导出旅行社列表
    Route::get('/ta/guides/export/{id}','TaController@exportTaGuides');//导出旅行社列表
    Route::post('/ta/add/','TaController@taAddInfo');//添加旅行社
    Route::post('/ta/state/{ta_id}/{state}','TaController@taChangeState');//修改旅行社状态

    //cuscomer
    Route::get('/cuscomer/suppliers','CuscomerController@supplierList');//供应商管理
    Route::get('/cuscomer/suppliers/{id}','CuscomerController@supplierSms');
    Route::get('/cuscomer/supplier','CuscomerController@supplierAdd');
    Route::post('/cuscomer/supplier','CuscomerController@supplierAdd');
    Route::get('/cuscomer/supplier/{id}','CuscomerController@editSupplier');
    Route::post('/cuscomer/supplier/{id}','CuscomerController@supplierEdit');

    Route::get('/cuscomer/guiders/{state?}','CuscomerController@guiderList');//导游管理
    Route::get('/cuscomer/guider/{id}','CuscomerController@guiderEdit');
    Route::get('/cuscomer/guider/check/{id}','CuscomerController@guiderCheck');//之后发送短信？？？？？？
    Route::post('/cuscomer/guider/send/{id}','CuscomerController@guideCheckSms');//发送短信
    Route::get('/cuscomer/salers','CuscomerController@salerList');//销售管理
    Route::get('/cuscomer/saler/{action}/{id}','CuscomerController@salerLook');
    Route::get('/cuscomer/saler','CuscomerController@salerAdd');
    Route::post('/cuscomer/saler','CuscomerController@salerAdd');
    Route::get('/cuscomer/saler/{id}','CuscomerController@salerEdit');


    //fund

    /*Route::get('/fund/buyfunds','FundController@buyfundList');//供应商管理*/

    Route::get('/fund/buyfunds','FundController@BuyFundList');//营业额列表
    Route::get('/fund/reportfund/{action?}','FundController@TransactionRecord');//交易记录
    Route::post('/fund/reportfund/{action?}','FundController@TransactionRecord');//交易记录
    Route::get('/fund/export/{action?}/{state?}/{type?}','FundController@export');//导出提现记录
    Route::get('/fund/record/export/{action?}/{state?}/{type?}','FundController@exportRecord');//导出交易记录


    //withdraw
    Route::get('/withdraw/guide/{state?}/{action?}','WithdrawController@TXManage');//提现管理
    Route::post('/withdraw/guide/{state?}/{action?}','WithdrawController@TXManage');//提现管理
    Route::get('/withdraw/txaudit/{actionId?}/{action?}/{id?}/{state?}/{amount?}','WithdrawController@TXAudit');//提现详情
    Route::post('/withdraw/txaudit/{actionId?}/{action?}/{id?}/{state?}/{amount?}','WithdrawController@TXAudit');//提现详情
    Route::get('/withdraw/export/{actionId?}/{action?}/{id?}/{state?}/{amount?}','WithdrawController@export');//提现详情导出

    Route::get('/withdraw/export/{actionId?}/{action?}/{id?}/{state?}/{amount?}','WithdrawController@export');//提现详情导出

    Route::get('/withdraw/sms/reject/{action}','WithdrawController@sendRejectMsg');//短信审核驳回通知结果
    Route::get('/withdraw/sms/pass/{action}','WithdrawController@sendPassMsg');//审核通过
    Route::get('/withdraw/finance/pass/','WithdrawController@financePass');//财务审核通过
    Route::get('/withdraw/sms/pay/{action}','WithdrawController@sendPayMsg');//短信通知打款结果
    Route::get('/withdraw/travel','WithdrawController@TXManage');//旅行社提现管理
    Route::get('/withdraw/price/change/record/{info}','WithdrawController@showPriceBuyingChangeRecord');//供应价改变记录
//    Route::get('/withdraw/supplier/{state?}','WithdrawController@supplierBilling');//供应商提现管理

//    Route::get('/fund/txaudit','FundController@TXAudit');//提现管理

    //数据报表
    Route::get('/datareport/index','DatareportController@index');
    Route::get('/datareport/goodssale','DatareportController@goodsSale');
    Route::get('/datareport/salepercent','DatareportController@goodsSalePercent');
    Route::get('/datareport/copnsale','DatareportController@copnSale');
    Route::get('/datareport/addmem','DatareportController@addMember');
    Route::get('/datareport/guidessale','DatareportController@guidesSale');
    Route::get('/datareport/tasale','DatareportController@taSale');
    Route::get('/datareport/guidebandmem','DatareportController@guideBandMember');
    Route::get('/datareport/tabandmem','DatareportController@taBandMember');
    Route::get('/datareport/guidebandh','DatareportController@guideBandHkmovie');
    
    
    

    //wx
    Route::get('/wx/reply/list/{category?}','WxController@replyPic');
    Route::get('/wx/reply/add','WxController@replyPicAdd');
    Route::post('/wx/reply/add','WxController@replyPicAdd');
    Route::get('/wx/reply/{id}/del','WxController@replyPicDel');
    Route::get('/wx/reply/{id}/update','WxController@replyPicUpdate');
    Route::post('/wx/reply/{id}/update','WxController@replyPicUpdate');

    Route::post('/wx/upload','WxController@replyUpload');
});

Route::group(['domain' => env('H5_DOMAIN'), 'namespace' => 'Wx'], function () {

    //wx公众号
    Route::any("/wx/api", 'ApiController@index');

    //index
    Route::get('/0', 'IndexController@index');//首页

    Route::get('/wx-index', 'IndexController@index');//首页
    Route::post('/city', 'IndexController@getCityByLngLat');//首页定位

    Route::get('/search', 'GoodsController@SearchGoods');//搜索
    Route::get('/pavilions/{id}', 'IndexController@index');//馆
    Route::get('/pavilionsLocation/{id}', 'PavilionController@indexLocation');//馆定位
    Route::get('/category/{category_id?}/{display_state?}', 'GoodsController@CategoryGoods');//分类
    Route::post('/category', 'GoodsController@categoryGoodsLimit');//分类分页

    Route::get('/pavilions', 'PavilionController@pavilionlists');
    Route::get('/pavilion/{id}/{display_state?}', 'PavilionController@pavilionGoods');//馆

    //cart
    Route::get('/cart', 'CartController@cartLists');//购物车
    Route::post('/cart', 'CartController@cartDel');
    Route::post('/cart/selected', 'CartController@cartSelected');
    Route::post('/carts', 'CartController@cartInsert');

    //order
    Route::post('/order/pay', 'OrderController@postOrder');
    Route::get('/order/pay', 'OrderController@payOrder');

    Route::any('/order/notify', 'OrderController@notifyPayOrder');

    Route::get('/order/cart/{id}', 'OrderController@getCartGoods');
    Route::get('/order/carts', 'OrderController@getCartNumGoods');

    Route::get('/goods/{id}', 'GoodsController@getGoodsDetail');//商品详情页面
    Route::get('/goods/comment/{goodsid}', 'GoodsController@allComment');//商品详情页面

    Route::post('/goods/{id}', 'GoodsController@handleGoodsPrice');

    //order
    Route::post('/order', 'OrderController@postOrder');
    Route::post('/orderCoupon', 'OrderController@OrderCoupon');
    //Route::get('/order/cart', 'OrderController@getCartGoods');

    Route::post('/goods/{id}', 'GoodsController@handleGoodsPrice');//商品不同规格的价格显示处理
    Route::get('/collect/', 'GoodsController@Collect');//商品收藏处理
    Route::get('/supplier/{id}', 'GoodsController@supplierGoodsList');//供应商产品列表
    Route::post('/supplierGoodPage', 'GoodsController@supplierGoodListPage');//供应商产品列表
    Route::get('/guide/{id}', 'GuidesController@GuidesGoodsList');//导游商品列表
    Route::post('/guideGoodPage', 'GuidesController@GuidesGoodsLimit');//导游商品列表分页
    Route::get('/mine', 'MineController@Mine');//个人中心
    Route::get('/collection/', 'MineController@MineCollection');//个人收藏列表
    Route::post('/collectLimit', 'MineController@goodCollectListPage');//个人收藏列表分页
    Route::post('/collection', 'MineController@MineCollectionDel');//个人收藏列表
    Route::get('/setting/', 'MineController@SettIng');//用户信息设置表SaveChanges
    Route::get('/save/', 'MineController@SaveChanges');//保存设置
    Route::get('/collection/', 'MineController@MineCollection');//个人收藏列表
    //coupon
    Route::get('/coupon/{state}', 'MineController@MineCoupon');//个人优惠券列表
    Route::get('/CouponGoods/{id}', 'MineController@CouponGoods');
    Route::get('/couponGiven', 'MineController@couponGiven');

    Route::get('/order/{state?}', 'MineController@Order');//订单列表
    Route::get('/detail/{id}', 'MineController@OrderPayDetail');//订单详情
    Route::get('/cancelorder/{id}', 'MineController@CancelOrder');//取消订单
    Route::get('/aftersales/{id}', 'MineController@AfterSales');//申请售后

    Route::post('/afterOrderState', 'MineController@AfterSalesState');//申请售后
    Route::post('/aftersales/{id}', 'MineController@handleAfterSales');//处理申请售后

    Route::get('/aftersaleslist/{id}', 'MineController@afterSalesList');//申请售后列表
    Route::get('/aftersalesDetail/{id}', 'MineController@afterSalesDetail');//售后详情
    Route::get('/aftersales/', 'MineController@afterSalesRefund');//售后退款
    Route::get('/finished/{id}', 'MineController@finishedOrder');//确认收货

    Route::post('/mine/upload', 'MineController@upload');//文件上传
    
    //我的评价
    Route::get('/mine/list', 'MineController@goodsCommentList');//加载完成的订单列表
    Route::get('/mine/comment/{id}', 'MineController@goodsComment');//加载评价页面
    Route::post('/mine/savecomment', 'MineController@saveComment');//进行评论的保存
    
    Route::get('/mine/detail/{orderno}', 'MineController@goodsCommentDetail');//加载评论详情



    Route::get('/order/{id}', 'MineController@Order');//保存设置

    //address
    Route::get('/addresses', 'AddressController@addressList');
    Route::get('/address', 'AddressController@addressAdd');
    Route::post('/address', 'AddressController@addressAdd');
    Route::get('/address/{id}', 'AddressController@addressEdit');
    Route::post('/addresses', 'AddressController@editAddress');
    Route::get('/addresses/{action}', 'AddressController@addressAction');

    Route::get('/test/index', 'TestController@index');
    Route::get('/test/html', 'TestController@html');

    Route::get('/wxLocation', 'WxLocationController@index');
    Route::post('/wxLocation/pic', 'WxLocationController@mediaPic');
});

/* 公共路由 */
//编辑器文件上传
Route::post('/ueditor_upload','ImageController@upload');

Route::group(['prefix' => 'v1','namespace' => 'Api'], function() {
    Route::post('/upload/image', 'ImageController@upload');
    Route::post('/order', 'OrderController@addOrder');
    Route::post('/order/multi', 'OrderController@addMultiOrder');
    Route::post('/order/multi_pay', 'OrderController@payMultiOrder');
    Route::post('/order/{order_no}/finish', 'OrderController@finishOrder');
    Route::post('/order/{order_no}/cancel', 'OrderController@cancelOrder');
    Route::post('/order/{order_no}/modify_amount', 'OrderController@modifyOrderAmount');
    Route::post('/order/{order_no}', 'OrderController@payOrder');
    Route::get('/order/pre_coupon', 'OrderController@getOrderPreCoupon');
    Route::get('/order/coupon', 'OrderController@getCouponByOrder');




    //cart
    Route::get('/cart', 'CartController@getCart');
    Route::post('/cart', 'CartController@addCart');
    Route::delete('/cart', 'CartController@deleteCart');
    Route::post('/cart/{id}', 'CartController@modifyCart');


    //config
    Route::get('/conf/banner','ConfController@getBanner');
    Route::get('/conf/search_word','ConfController@getSearchWord');
    Route::get('/conf/pavilion','ConfController@getPavilion');
    Route::get('/conf/category','ConfController@getCategory');
    Route::get('/conf/news','ConfController@getNews');
    Route::get('/conf/new/{id}','ConfController@getNewsById');
    Route::get('/conf/timestamp','ConfController@timestamp');
    Route::get('/conf/city','ConfController@getCity');
    Route::get('/conf/bank','ConfController@getBank');
    Route::get('/conf/android_version', 'ConfController@androidVersion');
    Route::get('/conf/ios_version', 'ConfController@iOSVersion');



    //user
    Route::post('/user/login', 'UserNoSignController@login');
    Route::post('/user/register', 'UserNoSignController@register');
    Route::post('/user/change_password', 'UserNoSignController@changePassword');
    Route::post('/user/recode', 'UserNoSignController@postRecode');
    Route::get('/user/recode_register', 'UserNoSignController@checkRegisterRecodeInviteCode');
    Route::get('/user/recode_forget_password', 'UserNoSignController@checkForgetPasswordRecode');
    Route::get('/user/pavilion', 'UserNoSignController@getPavilion');
    Route::get('/user/pavilion_list', 'UserNoSignController@getPavilionList');

    Route::post('/user/device', 'UserNoSignController@postDevice');


    //goods
    
    Route::get('/goods/list','GoodsController@getGoodsList');
    Route::get('/coupon/goods/','GoodsController@getGoodsListByCoupon');
    Route::get('/goods/theme_recommend','GoodsController@getThemeRecommend');
    Route::get('/goods/store/{id}','GoodsController@getSupplierStoreGoods');
    Route::get('/goods/{id}/detail', 'GoodsController@viewGoodsDetail');
    Route::get('/goods/{id}','GoodsController@getGoodsById');
    Route::get('/goods/{id}/comments','GoodsController@getGoodsComments');//获取商品的更多评论
    Route::get('/goods/{id}/material','GoodsController@getGoodsMaterial');
    

    //store
    Route::post('/store/goods','StoreController@deleteStoreGoods');
    Route::post('/store/{id}','StoreController@changStoreInfo');
    Route::post('/store/{id}/goods','StoreController@addStoreGoods');
    Route::get('/store/{id}','StoreController@getStoreInfo');
    Route::get('/store/{id}/goods','StoreController@getStoreGoods');
    Route::get('/store/{id}/users','StoreController@getStoreUsers');
    Route::get('/store/{id}/orders','StoreController@getStoreOrders');
    Route::get('/store/{id}/groups','StoreController@getTaGroups');


    Route::post('/guide/remark_name','StoreController@postGuideUserRemarkName');
    Route::post('/guide/{id}','StoreController@uploadGuideInfo');

    Route::Post('/group/{id}','StoreController@changTaGroupState');
    Route::get('/groups/orders','StoreController@getTaGroupOrders');



    //my
    Route::get('/my/order/{order_no}/comments','MyController@getOrderComment');//获取商品的评价
    Route::get('/my/orderlist','MyController@getCompletedOrderList');//获取完成订单的列表
    Route::get('/my/order/{id}', 'MyController@getOrderDetailById');
    Route::get('/my/orders','MyController@getOrders');
    Route::get('/my/badge','MyController@getBadge');
    Route::get('/my/favorite','MyController@getFavorite');
    Route::get('/my/coupon','MyController@getCoupon');
    Route::post('/my/favorite','MyController@addFavorite');
    Route::post('/my/favorite/{id}','MyController@deleteFavorite');

    Route::get('/my/bank_card/check','MyController@checkBankCard');
    Route::post('/my/bank_card','MyController@bindBankCard');
    Route::delete('/my/bank_card','MyController@deleteBankCard');

    Route::get('/my/withdraw/info','MyController@getWithdrawInfo');
    Route::get('/my/withdraw','MyController@getWithdraw');
    Route::post('/my/withdraw','MyController@addWithdraw');


    Route::get('/my/return_orders','MyController@getReturnOrders');
    Route::get('/my/return_order/{return_no}','MyController@getReturnOrderDetail');
    Route::post('/my/return_order','MyController@addReturnOrder');


    Route::get('/my/address','MyController@getAddress');
    Route::post('/my/address','MyController@addAddress');
    Route::post('/my/address/{id}','MyController@modifyAddress');
    Route::post('/my/address/{id}/delete','MyController@deleteAddress');

    Route::get('/my/wx_qrcode','MyController@getWxQrCode');
    Route::post('/my/comment','MyController@saveComments');
    
    //wx
    Route::get('/pavilion/banner_theme','PavilionController@getBannerTheme');

    Route::post('/pay/callback', 'PayNoSignController@callBack');

    Route::get('/wx/userInfo', 'WxController@getUserInfo');
    Route::get('/wx/sessionKey','WxController@getSessionKey');

});

Route::group(['domain' => env('H5_DOMAIN'), 'namespace' => 'Activity'], function () {
    Route::get('/activity/index','ActivityController@index');
});


Route::get('/','WelcomeController@index');




