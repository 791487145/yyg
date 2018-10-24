@extends('supplier')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>我要提现</span></h2>
            <div class="turnoverWrap">
                <form class="form orderDetail" id="withdraw">
                    <div class="box">
                        <div class="statisNum">
                            <div>￥<b>{{$user->amount}}</b></div>
                            <div>账户余额</div>
                        </div>
                        <div class="statisNum">
                            <div>￥<b>{{$user->amount}}</b></div>
                            <div>可提现余额</div>
                        </div>
                    </div>
                    <div class="box">
                        <h5>提现账户</h5>
                        <p>收款人姓名：{{$user->withdraw_name}}</p>
                        <p>银行卡号：{{$user->withdraw_card_number}}</p>
                        <p><span>{{$user->withdraw_bank}}</span><span>{{$user->withdraw_sub_bank}}</span></p>
                    </div>
                    <div class="box enchashForm">
                        <table>
                            <input type="hidden" value="{{$billingSourceIds}}" name="billingSourceIds">
                            <tr>
                                <th>提现金额：</th>
                                <td><input type="hidden" name="amount" value="{{$user->amount}}">{{$user->amount}}</td>
                                <td><input type="hidden" class="button" id="withdraw-all" value="全部提现"></td>
                            </tr>
                            <tr>
                                <th>注册手机号：</th>
                                <td>{{$user->mobile}}</td>
                                <td><input type="button" class="button" id="send" value="发送验证码"></td>
                            </tr>
                            <tr>
                                <th>验证码：</th>
                                <td><input type="text" name="mobile_code"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="footButton">
                        <input type="hidden" name="type" value="1">
                        <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                        <input type="submit" value="申请提交" class="button submit">
                    </div>
                </form>
            </div>

        </div>

    </div>
    </div>
    <div class="popupBg"></div>
    <div class="popupWrap confirmWrap applySuccess">
        <div class="title"><b>申请提现</b></div>
        <div>
            <h2>提现申请</h2>
            <p class="textC">恭喜你提现申请提交成功~</p>
            <p>我们的工作人员会在1~2个工作日内尽快审核，提现资金到账时
                间具体以银行到账时间为准！</p>
            <div class="buttonGroup">
                <input type="button" class="cancel" value="返回">
            </div>
        </div>
    </div>
    <script>
        window.onload=function(){
            var send=document.getElementById('send');
            var withdraw=document.getElementById('withdraw-all');
            withdraw.onclick = function(){
                $('[name=amount]').val('{{$user->amount}}');
            };
            send.onclick=function(){
                send.disabled=true;
                //发送短信
                $.get('/auth/sms',{'mobile':{{$user->mobile}},'type':1},function(json){
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
                    popupConfirm('applySuccess')
                } else {
                    layer.alert(data.msg, {icon:2,time:5000});
                }
            }
        });
        $(".applySuccess .buttonGroup .cancel").click(function(){
            $(".popupBg,.popupWrap").hide();
            location.reload();
        });
    </script>
    @stop
