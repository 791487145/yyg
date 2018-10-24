<!DOCTYPE html>
<html lang="Zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
    <title>易游购</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/yyg-supplier.css')}}?v=<?php echo date('Ymdhi')?>">
    <script type="text/javascript" src="{{asset('js/jquery-1.11.0.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/lib/Validform/5.3.2/Validform_v5.3.2_min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/layer/2.1/layer.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/yyg-supplier.js')}}?v=<?php echo date('Ymdhi')?>"></script>
    <style>
        .leftSide{width:140px;}
        .rightCon{margin-left: 50px;}
        .logout{font-size: 12px;color: white;}
        #Validform_msg{display: none !important;}
        .Validform_right{display: none;}
        .Validform_wrong{color: red; background: url({{asset('images/error.png')}}) no-repeat left center;padding-left: 20px;}
    </style>
</head>
<body>
<div class="myWrap">
<div class="leftSide">
    <div class="headImg">
    	<a href="{{url('/supplier/set')}}">
        @if($supplier->store_logo)
        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$supplier->store_logo}}">
            @else
            <img src="{{asset('/images/img-null.jpg')}}">
        @endif
        </a>
        <br />
        <b class="logout" >{{isset($supplier->store_name)?$supplier->store_name:''}}</b>
        <br />
    </div>
    <ul class="nav">
        <li @if($controller == 'DashboardController')class="active" @endif>
            <a href="{{url('/')}}"><i></i>首页概况</a>
        </li>
        <li @if($controller == 'GoodsController')class="active" @endif><a href="javascript:void(0)"><i></i>商品管理</a>
            <ul>
                <li @if($action == 'create' && $controller == 'GoodsController') class="active" @endif><a href="{{url('/goods/create')}}">发布商品</a></li>
                <li @if(($action == 'review' && $controller == 'GoodsController') || ($action == 'reviewEdit' && $controller == 'GoodsController')|| ($action == 'reviewShow' && $controller == 'GoodsController')) class="active" @endif><a href="{{url('/goods/review')}}">商品审核</a></li>
                <li @if(($action == 'index' && $controller == 'GoodsController') || ($action== 'edit' && $controller == 'GoodsController')|| ($action== 'show' && $controller == 'GoodsController'))class="active" @endif><a href="{{url('/goods')}}">商品库</a></li>
            </ul>
        </li>
        <li  @if($controller == 'SaleManageController')class="active" @endif><a href="javascript:void(0)"><i></i>营销管理</a>
            <ul>
                <li @if(($action == 'goodmaterial' && $controller == 'SaleManageController')) class="active" @endif><a href="{{url('/material/goodmaterial')}}">商品素材</a></li>
                <li @if(($action == 'supplierExpressList' && $controller == 'SaleManageController')) class="active" @endif><a href="{{url('/supplierExpress')}}">邮费设置</a></li>
            </ul>
        </li>
        <li  @if($controller == 'OrderController')class="active" @endif><a href="javascript:void(0)"><i></i>订单管理</a>
            <ul>
                <li @if(($action == 'deliverys' && $controller == 'OrderController') || ($action == 'getDelivery' && $controller == 'OrderController') || ($action == 'getImport' && $controller == 'OrderController')) class="active" @endif><a href="{{url('/order/deliverys/0')}}">发货单</a></li>
                <li @if(($action == 'aftersales' && $controller == 'OrderController') || ($action == 'aftersale' && $controller == 'OrderController')) class="active" @endif><a href="{{url('order/aftersales')}}">售后订单</a></li>
                <li @if(($action == 'all' && $controller == 'OrderController') || ($action == 'show' && $controller == 'OrderController')) class="active" @endif><a href="{{url('order/all')}}">全部订单</a></li>
            </ul>
        </li>
        

        <li  @if($controller == 'CommentController')class="active" @endif><a href="javascript:void(0)"><i></i>我的评价</a>
            <ul>
                <li @if($action == 'index' && $controller == 'CommentController')class="active" @endif><a href="{{url('comment/index')}}">评价列表</a></li>
            </ul>
        </li>
        <li  @if($controller == 'FundController')class="active" @endif><a href="javascript:void(0)"><i></i>资金管理</a>
            <ul>
                <li @if($action == 'index' && $controller == 'FundController') class="active" @endif><a href="{{url('/fund')}}">营业额</a></li>
                <li @if(($action == 'record' && $controller == 'FundController') || ($action == 'show' && $controller == 'FundController')) class="active" @endif><a href="{{url('/fund/record')}}">交易记录</a></li>
                <li @if(($action == 'withdraw' && $controller == 'FundController') || ($action == 'apply' && $controller == 'FundController')) class="active" @endif><a href="{{url('/fund/withdraw')}}">提现记录</a></li>
            </ul>
        </li>
        <li  @if($controller == 'SupplierController')class="active" @endif><a href="javascript:void(0)"><i></i>个人设置</a>
            <ul>
                <li @if(($action == 'set' && $controller == 'SupplierController')) class="active" @endif><a href="{{url('/supplier/set')}}">个人设置</a></li>
                <li @if(($action == 'getPassword' && $controller == 'SupplierController')) class="active" @endif><a href="{{url('/supplier/password')}}">修改密码</a></li>
            </ul>
        </li>
        {{--<li><div class="headImg"><b></b>--}}
                {{--<a class="logout" href="{{url('/auth/logout')}}">退出</a></div></li>--}}
    </ul>
</div>
    @yield('content')
</div>

@yield('footer')


</body>
</html>