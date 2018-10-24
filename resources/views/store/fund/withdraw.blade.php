@extends('supplier')
@section('content')

    <div class="rightCon">
        <div class="wrap">
            <h2>
            	<span>提现记录</span>
            </h2>
            <div style="margin:20px;">
            	@if(isset($user->withdraw_card_number))
            		{{--<a class="but-yes" href="{{url('fund/apply')}}">申请提现</a>--}}
            		<a class="but-no" href="javascript:void(0)" onclick="popupConfirm('enchashAccount')">设置提现账户</a>
            	@else
            		{{--<a class="but-yes" href="javascript:void(0)" onclick="popupConfirm('enchashAccountEdit')">申请提现</a>--}}
            		<a class="but-no" href="javascript:void(0)" onclick="popupConfirm('enchashAccountEdit')">设置提现账户</a>
            	@endif
                
            </div>
            <table class="goodsTable fundTable enchashTable">
                <tr>
                    <th>提交时间</th>
                    <th>账户余额</th>
                    <th>提现金额</th>
                    <th>提现账户信息</th>
                    <th>状态</th>
                </tr>
                <tr>
                    @forelse($withdraws as $withdraw)
                    <td>{{$withdraw->created_at}}</td>
                    <td>{{$withdraw->balance}}</td>
                    <td>{{$withdraw->amount}}</td>
                    <td>
                        <p>{{isset($withdraw->info->withdraw_bank)?$withdraw->info->withdraw_bank:''}}</p>
                        <p>{{isset($withdraw->info->withdraw_card_number)?$withdraw->info->withdraw_card_number:''}}</p>
                        <p>{{isset($withdraw->info->withdraw_name)?$withdraw->info->withdraw_name:''}}</p></td>
                    <td>{{\App\Models\SupplierBilling::getStateName($withdraw->state)}}</td>
                </tr>
                @empty
                    @endforelse

            </table>
            <div class="footPage">
                <p>共{{$withdraws->lastPage()}}页,{{$withdraws->total()}}条数据 ；每页显示{{$withdraws->perPage()}}条数据</p>
                <div class="pageLink">
                    {!! $withdraws->render() !!}
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="popupBg"></div>
    <div class="popupWrap enchashAccount">
        <div class="title"><b>提现账户</b></div>
        <div class="enchashAccountForm">
            <dl>
                <dt>账户名：</dt>
                <dd><input type="text" value="{{isset($user->withdraw_name)?$user->withdraw_name:''}}" disabled></dd>
            </dl>
            <p class="warning">*账户名必须与签订合同的真实姓名保持一致，否则无法提现</p>
            <dl>
                <dt>银行卡号：</dt>
                <dd><input type="text" value="{{isset($user->withdraw_card_number)?$user->withdraw_card_number:''}}" disabled></dd>
            </dl>
            <dl>
                <dt>提现银行：</dt>
                <dd><input type="text" value="{{isset($user->withdraw_bank)?$user->withdraw_bank:''}}" disabled></dd>
            </dl>
            <dl>
                <dt>银行支行：</dt>
                <dd><input type="text" value="{{isset($user->withdraw_sub_bank)?$user->withdraw_sub_bank:''}}" disabled></dd>
            </dl>
        </div>
        <div class="buttonGroup">
            <input type="button" class="cancel" value="取消">
            <input type="button" class="submit" value="编辑" onclick="popupConfirm('enchashAccountEdit')">
        </div>
    </div>
    <div class="popupWrap enchashAccountEdit">
        <form action="{{url('fund/withdraw')}}" method="post" id="withdraw">
        <div class="title"><b>提现账户</b></div>
        <div class="enchashAccountForm">
            <dl>
                <dt>账户名：</dt>
                <dd><input type="text" name="withdraw_name" datatype="*" value="{{isset($user->withdraw_name)?$user->withdraw_name:''}}" ></dd>
            </dl>
            <p class="warning">*账户名必须与签订合同的真实姓名保持一致，否则无法提现</p>
            <dl>
                <dt>银行卡号：</dt>
                <dd><input type="text" name="withdraw_card_number" value="{{isset($user->withdraw_card_number)?$user->withdraw_card_number:''}}"></dd>
            </dl>
            <dl>
                <dt>选择银行：</dt>
                <dd>
                    <select name="withdraw_bank">
                        @forelse($banks as $bank)
                            <option value="{{$bank->name}}" @if($bank->name == $name = isset($user->withdraw_bank)?$user->withdraw_bank:'') selected @endif>{{$bank->name}}</option>
                        @empty
                        @endforelse
                    </select>
                </dd>
            </dl>
            <dl>
                <dt>填写支行：</dt>
                <dd><input type="text" name="withdraw_sub_bank" value="{{isset($user->withdraw_sub_bank)?$user->withdraw_sub_bank:''}}"></dd>
            </dl>
            <dl>
                <dt>注册手机号：</dt>
                <dd>{{$user->mobile}} <input type="button" value="发送验证码" id="send"></dd>
            </dl>
            <dl>
                <dt>短信验证码：</dt>
                <dd><input type="text" name="mobile_code"></dd>
            </dl>
        </div>
        <div class="buttonGroup">
            <input type="hidden" name="type" value="1">
            <input type="button" class="cancel" value="取消">
            <input type="button" class="withdraw-submit" value="@if(isset($user->withdraw_card_number)) 重新提交 @else 确认提交 @endif">
        </div>
        </form>
    </div>
    <script>
        window.onload=function(){
            var send=document.getElementById('send');
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
                    layer.alert(data.msg,{icon:1,time:1000});
                    window.location.reload();
                } else {
                    layer.alert(data.msg, {icon:2,time:5000});
                }
            }
        });
        $(".popupWrap .buttonGroup .withdraw-submit").click(function(){
            //$(".popupBg,.popupWrap").hide();
            $('#withdraw').submit();
        });


    </script>
    @stop
