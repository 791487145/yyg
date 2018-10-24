<!DOCTYPE html>
<html lang="Zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>易游购-注册</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/yyg-lxs.css')}}">
    <script type="text/javascript" src="{{asset('js/jquery-1.11.0.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/yyg-travel.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/yyg-travel.css')}}">
    <script type="text/javascript" src="{{asset('/lib/Validform/5.3.2/Validform.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/layer/2.1/layer.js')}}"></script>
</head>

<body>
<div class="loginHeader">
    <img src="{{asset('images/register.png')}}"/>
    <a href="/auth/login">已有帐号，立即登录</a>
</div>
<div class="forgetPassword">
    <form action="{{url('/auth/register')}}" method="post" id="form-auth-login">
        <input type="hidden" name="type" value="1">
        <ul>
            <li><input type="text" name="mobile" placeholder="输入手机号" datatype="/^1[34578]\d{9}$/" maxlength="11"
                       nullmsg="请输入手机号码" errormsg="请输入手机号码" onkeyup="this.value=this.value.replace(/\D/g,'')"
                       onafterpaste="this.value=this.value.replace(/\D/g,'')"><input type="hidden" name="invite_code"
                                                                                     value="{{$invite_code}}"><br/></li>

            <li class="identNumber"><input type="text" name="code" placeholder="输入验证码" maxlength="6"><input
                        type="button" value="发送验证码" id="sendYZM" autocomplete="off"><br/></li>
            <li><input type="password" placeholder="请设置新的登入密码（至少6位数不限字符）" name="password" datatype="*6-40"
                       maxlength="40"
                       nullmsg="请输入密码"><br/></li>
            <li><input type="password" placeholder="请确认新的登入密码（至少6位数不限字符）" name="passwords" datatype="*6-40"
                       maxlength="40"
                       nullmsg="请输入确认密码" recheck="password" errormsg="您两次输入的账号密码不一致！"><br/></li>
            <li><input style="background:#fe5c03;width: 328px;color: #fff;border: 0" type="button" value="登入"
                       class="submit"></li>
        </ul>
    </form>
</div>
<div class="loginFooter">
    <p>©yyougo.com</p>
</div>
</body>
<script type="text/javascript">

    $("#sendYZM").click(function () {
        this.disabled = true;
        var mobile = $("input[name = 'mobile']").val();
        if (sms()) {
            $.get('/auth/sms', {mobile: mobile, type: 1}, function (mess) {
                if (mess.ret == 'no') {
                    layer.alert(mess.msg, {icon: 2, time: 3000});
                }
                if (mess.ret == 'yes') {
                    layer.msg(mess.msg, {icon: 1, time: 1000});
                    time();
                }
            })
        }
    })

    function sms() {
        var mobile = $("input[name = 'mobile']").val();
        if (!(/^1[34578]\d{9}$/.test(mobile))) {
            layer.alert('手机号码有误，请重填!', {icon: 2, time: 3000});
            return false;
        } else {
            return true;
        }
    }
    $('.submit').click(function () {
        if (sms()) {
            var paw = $("input[name = 'password']").val();
            if (paw.length <= 5) {
                layer.alert('密码至少六位，请重填!', {icon: 2, time: 3000});
            } else {
                $("#form-auth-login").submit();
            }

        }

    });

    $("#form-auth-login").Validform({
        tiptype: 3,
        ajaxPost: true,
        postonce: true,
        callback: function (data) {
            if (data.ret == 'yes') {
                layer.msg(data.data, {icon: 1, time: 1000});
                location.href = '/auth/login';
            }
            if (data.ret == 'no') {
                layer.alert(data.data, {icon: 2, time: 3000});
            }
        }
    });

    function time() {
        var send = document.getElementById('sendYZM'),
            time = 60;
        times = time,
            timer = null;
        // 计时开始
        timer = setInterval(function () {
            times--;
            if (times <= 0) {
                send.defaultValue = '发送验证码';
                clearInterval(timer);
                send.disabled = false;
                times = time;
            } else {
                send.defaultValue = times + 's'
                send.disabled = true;
            }
        }, 1000)
    }
</script>
</html>