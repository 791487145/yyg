<!DOCTYPE html>
<html lang="Zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>易游购-找回密码</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/yyg-travel.css')}}">
    <script type="text/javascript" src="{{asset('js/jquery-1.11.0.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/lib/Validform/5.3.2/Validform.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/layer/2.1/layer.js')}}"></script>
</head>
<style>
    #Validform_msg{display: none;}
</style>
<body style="background: #fff;">
<div class="loginHeader">
    <img src="{{asset('images/forget.png')}}" />
    <a href="{{url('auth/login')}}">立即登录</a>
</div>
<div class="forgetPassword">
    <form method="post" id="forget" action="{{url('auth/forget')}}">
        <input type="hidden" name="type" value="2">
        <ul>
            <li><input type="text" name="mobile" placeholder="输入手机号" datatype="/^1[34578]\d{9}$/" maxlength="11"
                       nullmsg="请输入手机号码" errormsg="请输入手机号码" onkeyup="this.value=this.value.replace(/\D/g,'')"
                       onafterpaste="this.value=this.value.replace(/\D/g,'')"><br/></li>
            <li class="identNumber"><input type="text" name="mobile_code" placeholder="输入验证码" maxlength="6"><input
                        type="button" value="发送验证码" id="send" autocomplete="off"><br/></li>
            <li><input type="password" placeholder="请设置新的登入密码（至少6位数不限字符）" name="password" datatype="*6-40"
                       maxlength="40"
                       nullmsg="请输入密码"><br/></li>
            <li><input type="password" placeholder="请确认新的登入密码（至少6位数不限字符）" name="password_confirm" datatype="*6-40"
                       maxlength="40"
                       nullmsg="请输入确认密码" recheck="password" errormsg="您两次输入的账号密码不一致！"><br/></li>
            <li><input type="submit" value="修改"></li>
        </ul>
    </form>
</div>
<div class="loginFooter">
    <p>©yyougo.com</p>
</div>

</body>
<script>
    window.onload=function(){
        var send=document.getElementById('send');
        send.onclick=function(){
            if(!$('[name=mobile]').val()){
                alert('请填写手机号码');
                return false;
            }
            send.disabled=true;
            //发送短信
            $.get('/auth/sms',{mobile:$('[name=mobile]').val(),'type':2},function(json){
                if(json.ret == 'yes'){
                    layer.alert(json.msg,{icon:1,time:2000});
                }else{
                    layer.alert(json.msg,{icon:2,time:2000});
                }
            });
            var times=60,
                timer=null;
            // 计时开始
            timer=setInterval(function(){
                times--;
                if(times > 0){
                    send.value = times+'s后重新发送';
                }else{
                    send.value ='发送验证码';
                    send.removeAttribute('disabled');
                    clearInterval(timer);
                }
            },1000)

        }
    };
    $("#forget").Validform({
        tiptype:3,
        ajaxPost:true,
        postonce:true,
        callback:function(data){
            if(data.ret == 'yes') {
                layer.alert(data.msg,{icon:1,time:1000});
                window.location.href = '{{url('auth/login')}}';
            } else {
                layer.alert(data.msg, {icon:2,time:5000});
            }
        }
    });
</script>
</html>