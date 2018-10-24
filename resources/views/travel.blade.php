<!DOCTYPE html>
<html lang="Zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
    <title>易游购</title>
    <script type="text/javascript" src="{{asset('js/jquery-1.11.0.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/yyg-travel.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/yyg-travel.css')}}?v=<?php echo date('Ymdhi')?>">
    <script type="text/javascript" src="{{asset('/lib/Validform/5.3.2/Validform_v5.3.2_min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/layer/2.1/layer.js')}}"></script>
    <style>
        .leftSide{width:140px;}
        .rightCon{margin-left: 50px;}
        .logout{font-size: 12px;color: white;}
    </style>
</head>
<body>
<div class="myWrap">
    <div class="leftSide">
        <div class="headImg">
        	<a href="{{url('/system/set/')}}">
            @if($travel->ta_logo)
                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$travel->ta_logo}}">
            @else
                <img src="{{asset('/images/img-null.jpg')}}">
            @endif
            </a>
            <br />
            <b class="logout" >{{isset($travel->ta_name)?$travel->ta_name:''}}</b>
            <br />
        </div>
        <ul class="nav">
            <li @if($controller == 'DashboardController')class="active" @endif>
                <a href="/"><i></i>首页概况</a>
            </li>
            <li @if($controller == 'ManageController')class="active" @endif><a href="javascript:void(0)"><i></i>用户管理</a>
                <ul>
                    <li @if(($action == 'guides' && $controller == 'ManageController') || ($action == 'guide' && $controller == 'ManageController')) class="active" @endif><a href="{{url('/manage/guides/')}}">导游管理</a></li>
                    <li @if(($action == 'visitors' && $controller == 'ManageController') || ($action == 'visitors_order' && $controller == 'ManageController')) class="active" @endif><a href="{{url('/manage/visitors/')}}">建团管理</a></li>
                </ul>
            </li>
            <li @if($controller == 'FundController')class="active" @endif><a href="javascript:void(0)"><i></i>资金管理</a>
                <ul>
                    <li @if($controller == 'FundController' && $action == 'myincome')class="active" @endif><a href="{{url('fund/myincome')}}">我的收益</a></li>
                    <li @if(($action == 'incomes' && $controller == 'FundController') || ($action == 'income' && $controller == 'FundController')) class="active" @endif><a href="{{url('fund/incomes/0')}}">收益明细</a></li>
                    <li @if(($action == 'withdraws' && $controller == 'FundController') || ($action == 'apply' && $controller == 'FundController')) class="active" @endif><a href="{{url('fund/withdraws')}}">提现记录</a></li>
                </ul>
            </li>
            <li @if($controller == 'SystemController')class="active" @endif><a href="javascript:void(0)"><i></i>个人设置</a>
                <ul>
                    <li @if(($action == 'set' && $controller == 'SystemController') || ($action == 'authentication' && $controller == 'SystemController') || ($action == 'authenticate' && $controller == 'SystemController')) class="active" @endif><a href="{{url('/system/set/')}}">个人设置</a></li>
                    <li @if(($action == 'password' && $controller == 'SystemController')) class="active" @endif><a href="{{url('/system/password/')}}">修改密码</a></li>
                </ul>
            </li>
            {{--<li><div class="headImg"><b></b>--}}
                    {{--<a class="logout" href="{{url('/auth/logout')}}">退出</a></div></li>--}}
        </ul>
    </div>
    @yield('content')
</div>
</body>
</html>