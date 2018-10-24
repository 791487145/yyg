<!DOCTYPE html>
<html lang="Zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>易游购-旅行社</title>
    <script type="text/javascript" src="{{asset('js/jquery-1.11.0.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/yyg-travel.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/yyg-travel.css')}}?v=<?php echo date('Ymdhi')?>">
    <script type="text/javascript" src="{{asset('/lib/Validform/5.3.2/Validform.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/layer/2.1/layer.js')}}"></script>
    <style>
        .loginCon {
            background: url({{asset('/images/login_bg_travel.png')}}) no-repeat center top #fff;
        }

        .user {
            background: url({{asset('/images/login_icon_07.png')}}) no-repeat !important;
        }

        .password {
            background: url({{asset('/images/login_icon_10.png')}}) no-repeat !important;
        }
    </style>
</head>

<body>
<div class="login">
    <div class="header">
        <div class="loginWrap">
            <a href="http://www.yyougo.com/"><img src="{{asset('/images/login_logo.png')}}"></a>
        </div>
    </div>
    <div class="loginCon">
        <div class="loginWrap">
            <div class="loginBox">
                <form action="{{url('/auth/login')}}" method="post" id="form-auth-login">
                    <div class="headImg"><img src="{{asset('/images/login_head_img_03.png')}}" alt="头像"></div>
                    <label class="user">
                        <input type="text" placeholder="请输入手机号码" name="account" datatype="/^1[34578]\d{9}$/"
                               maxlength="11" nullmsg="请输入手机号码" errormsg="请输入手机号码"
                               onkeyup="this.value=this.value.replace(/\D/g,'')"
                               onafterpaste="this.value=this.value.replace(/\D/g,'')">
                    </label>
                    <label class="password"><input type="password" placeholder="请输入密码" name="password" datatype="*6-20"
                                                   maxlength="20" nullmsg="请输入密码" errormsg="请输入6位以上密码"></label>
                    <input type="button" value="登录" class="submit">
                    <div class="loginLink">
                        <a href="{{url('auth/forget')}}">忘记密码</a>
                        <!-- <a href="{{url('/auth/register')}}">免费注册</a> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.submit').click(function () {
            $("#form-auth-login").submit();
        });
        $("#form-auth-login").Validform({
            tiptype: 3,
            ajaxPost: true,
            postonce: true,
            callback: function (data) {
                if (data.ret == 'yes') {
                    location.href = '/';
                } else if (data.ret == 'no') {
                    layer.alert(data.msg, {icon: 2});
                } else {
                    layer.alert('登录失败', {icon: 2});
                }
            }
        });
    });
</script>
</body>
</html>