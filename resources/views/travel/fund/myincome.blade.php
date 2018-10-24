@extends('travel')
@section('content')
    <style>
        #send{font-size:14px;width: 104px;}
        .Validform_checktip{position: absolute;line-height: 28px;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <h2><span>我的收益</span></h2>
            <div class="turnoverWrap">
                <div class="box">
                    <div class="statisNum">
                        <div>￥<b>{{$amount}}</b></div>
                        <div>今日销售额</div>
                    </div>
                    <div class="statisNum">
                        <div>￥<b>{{$totalSales}}</b></div>
                        <div>累计销售额</div>
                    </div>
                    <div class="statisNum">
                        <div>￥<b>{{$billing}}</b></div>
                        <div>待入账余额</div>
                    </div>
                    <div class="statisNum">
                        <div>￥<b>{{$user->amount}}</b></div>
                        <div>账户余额</div>
                    </div>
                    <div class="footButton">
	                    @if($user->state == 1)
                            <input type="hidden" name="billingSourceID" value="{{$IncomeAmountSource}}">
	                        <input type="button" class="save" value="我要提现" onclick="withdraw();">
	                    @elseif($user->state == 2)
	                        <input type="button" class="save" value="我要提现" onclick="popupConfirm('goChecking');">
	                        @else
	                        <input type="button" class="save" value="我要提现"  onclick="popupConfirm('goAuthentication')" >
	                    @endif
	                </div>
                </div>
               
            </div>
        </div>

    </div>
    </div>
    <div class="popupBg"></div>
    <div class="popupWrap enchashAccountEdit">
        <div class="title"><b>请添加提现账户</b></div>
        <div class="enchashAccountForm">
            <form action="{{url('fund/withdraw')}}" method="post" id="withdraw">
                <dl>
                    <dt>账户名：</dt>
                    <dd><input type="text" value="{{isset($user->withdraw_name)?$user->withdraw_name:''}}" disabled></dd>
                </dl>
                <p class="warning">*账户名必须与签订合同的真实姓名保持一致，否则无法提现</p>
                <dl>
                    <dt>银行卡号：</dt>
                    <dd><input type="text" name="withdraw_card_number" maxlength="19" datatype="n16-19" nullmsg="请输入银行卡号" errormsg="请输入银行卡号" onkeyup="this.value=this.value.replace(/\D/g,'')"
                               onafterpaste="this.value=this.value.replace(/\D/g,'')" ></dd>
                </dl>
                <dl>
                    <dt>选择银行：</dt>
                    <dd>
                        <select name="withdraw_bank">
                            @forelse($banks as $bank)
                                <option value="{{$bank->name}}">{{$bank->name}}</option>
                            @empty
                            @endforelse
                        </select>

                </dl>
                <dl>
                    <dt>填写支行：</dt>
                    <dd><input type="text" name="withdraw_sub_bank" datatype="s1-12" nullmsg="请输入支行" errormsg="请输入支行" maxlength="12"></dd>
                </dl>
                <dl>
                    <dt>注册手机号：</dt>
                    <dd>{{$user->mobile}}<input type="button" id="send" value="发送验证码"></dd>
                </dl>
                <dl>
                    <dt>短信验证码：</dt>
                    <dd><input type="text" name="mobile_code" maxlength="6" datatype="n6-6" nullmsg="请输入验证码" errormsg="请输入验证码"
                               onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"></dd>
                </dl>
                <input type="hidden" name="type" value="4">
            </form>
        </div>
        <div class="buttonGroup">
            <input type="button" class="cancel" value="取消">
            <input type="button" class="submit" value="确定提交">
        </div>
    </div>
    <div class="popupWrap confirmWrap submitSuccess">
        <div class="title"><b>提交提示</b></div>
        <div>
            <h2>提交提示</h2>
            <p class="textC">你的提现账户提交成功，赶紧去提现吧~</p>
            <div class="buttonGroup">
                <input type="button" class="button" value="去提现" onclick="window.location='{{url('fund/apply')}}'">
            </div>
        </div>
    </div>
    <div class="popupWrap confirmWrap goAuthentication">
        <div class="title"><b>提示</b></div>
        <div>
            <p class="textC">系统检查到您还没有实名认证，请先实名认证~</p>
            <div class="buttonGroup">
                <input type="button" class="button" value="去实名认证" onclick="window.location='{{url('system/authentication')}}'">
            </div>
        </div>
    </div>
    <div class="popupWrap confirmWrap goChecking">
        <div class="title"><b>提示</b></div>
        <div>
            <p class="textC">正在实名认证审核中，暂不能提现</p>
            <div class="buttonGroup">
                <input type="button" class="cancel" value="知道了" >
            </div>
        </div>
    </div>
    <script>
        window.onload=function(){
            var send=document.getElementById('send');
            send.onclick=function(){
                send.disabled=true;
                //发送短信
                $.get('/auth/sms',{'mobile':{{$user->mobile}},'type':4},function(json){
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
        $("#withdraw").Validform({
            tiptype:3,
            ajaxPost:true,
            postonce:true,
            callback:function(data){
                if(data.ret == 'yes') {
                    popupConfirm('submitSuccess');
                } else {
                    layer.alert(data.msg, {icon:2,time:5000});
                }
            }
        });
        $(".popupWrap .buttonGroup .submit").click(function(){
            //$(".popupBg,.popupWrap").hide();
            $('#withdraw').submit();
        });
        function withdraw(){
            var billingSourceID = $("input[name='billingSourceID']").val();
            var card_num = '{{$user->withdraw_card_number}}';
            if (!card_num){
                popupConfirm('enchashAccountEdit');
            }else{
                window.location.href = "{{url('fund/apply/?IncomeAmountSource=')}}"+billingSourceID;
            }


        }

    </script>
    @stop
