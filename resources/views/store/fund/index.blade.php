@extends('supplier')
@section('content')
    <style>
        #send{font-size:14px;width: 104px;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <h2><span>营业额</span></h2>
            @if ($errors->first())
                <span class="status-msg error-msg">{{ $errors->first() }}</span>
            @endif
            <div class="turnoverWrap">
                <div class="box">
                    <div class="statisNum">
                        <div>￥<b>{{$data['amountToday']}}</b></div>
                        <div>今日成交额</div>
                    </div>
                    <div class="statisNum">
                        <div><b>{{$data['orderCount']}}</b></div>
                        <div>今日成交单数</div>
                    </div>
                    <div class="statisNum">
                        <div>￥<b>{{$data['amount']}}</b></div>
                        <div>累计营业额</div>
                    </div>
                    <div class="statisNum">
                        <div>￥<b>{{$supplier->deposit}}</b></div>
                        <div>保证金</div>
                    </div>
                </div>
                <div class="box">
                	<div class="statisNum">
                        <div><b>{{$data['unAmount']}}</b></div>
                        <div>待入账金额</div>
                    </div>
                    <div class="statisNum">
                        <div>￥<b>{{$supplier->amount}}</b></div>
                        <div>账户余额</div>
                    </div>
                    <div class="footButton">
	                    @if($supplier->withdraw_card_number)
	                        <input type="button" class="save" value="我要提现" onclick="withdraw();">
	                    @else
	                        <input type="button" class="save" value="我要提现" onclick="popupConfirm('enchashAccountEdit');">
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
                <dd><input type="text" value="{{isset($supplier->withdraw_name)?$supplier->withdraw_name:''}}" disabled></dd>
            </dl>
            <p class="warning">*账户名必须与签订合同的真实姓名保持一致，否则无法提现</p>
            <dl>
                <dt>银行卡号：</dt>
                <dd><input type="text" name="withdraw_card_number"></dd>
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
                <dd><input type="text" name="withdraw_sub_bank"></dd>
            </dl>
            <dl>
                <dt>注册手机号：</dt>
                <dd>{{$supplier->mobile}}<input type="button" id="send" value="发送验证码"></dd>
            </dl>
            <dl>
                <dt>短信验证码：</dt>
                <dd><input type="text" name="mobile_code"></dd>
            </dl>
                <input type="hidden" name="type" value="1">
            </form>
        </div>
        <div class="buttonGroup">
            <input type="button" class="cancel" value="取消">
            <input type="button" class="submit" value="确定提交">
        </div>
    </div>
    <script>
        window.onload=function(){
            var send=document.getElementById('send');
            send.onclick=function(){
                send.disabled=true;
                //发送短信
                $.get('/auth/sms',{'mobile':{{$supplier->mobile}},'type':1},function(json){
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
            tiptype:2,
            ajaxPost:true,
            postonce:true,
            callback:function(data){
                if(data.ret == 'yes') {
                    layer.alert(data.msg,{icon:1,time:1000});
                    location.reload();
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
            window.location.href = '{{url('fund/apply/?billingSourceIds='.$billingSourceIds)}}'
        }

    </script>
@stop
