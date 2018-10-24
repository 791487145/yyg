@extends('travel')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>修改密码</span></h2>

            <form action="{{url('system/password')}}" method="post" class="form">
                <div class="box">
                    <h4>修改密码</h4>
                    <ul>
                        <li><p>你将使用<span>{{$user->mobile}}</span>手机号接受短信验证</p></li>
                        <li class="identNumber">
                            <input type="text" placeholder="输入验证码" name="mobile_code" maxlength="6" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                            <input type="button" value="发送验证码" id="send" autocomplete="off"></li>

                        <li>
                            <input type="password" placeholder="请设置新的登入密码（至少6位数不限字符）" datatype="*6-40" maxlength="40"
                                   nullmsg="请输入密码" name="password">
                        </li>

                        <li><input type="password" placeholder="请确认新的登入密码（至少6位数不限字符）" datatype="*6-40" maxlength="40"
                                   nullmsg="请输入确认密码" recheck="password" errormsg="您两次输入的账号密码不一致！" name="password_confirm">
                            <span></span>
                        </li>

                    </ul>
                </div>
                <div class="footButton">
                    <input type="hidden" name="type" value="3">
                    <input type="submit" value="保存">
                    <a href="{{url('auth/logout')}}" class="but-no" style="width: 140px;">退出账号</a>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script>
        window.onload=function(){
            var send=document.getElementById('send');
            send.onclick=function(){
                send.disabled=true;
                //发送短信
                $.get('/auth/sms',{mobile:'{{$user->mobile}}','type':3},function(json){
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
        }
        $(".form").Validform({
            tiptype:3,
            ajaxPost:true,
            postonce:true,
            callback:function(data){
                if(data.ret == 'yes') {
                    layer.alert(data.msg,{icon:1,time:1000});
                    window.location.href= '{{url('auth/logout')}}';
                } else {
                    layer.alert(data.msg, {icon:2,time:5000});
                }
            }
        });

    </script>
@stop
