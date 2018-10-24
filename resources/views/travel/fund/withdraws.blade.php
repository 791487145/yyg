@extends('travel')
@section('content')
    <style>
        #send{width:105px;}
        .Validform_checktip{position: absolute;line-height: 28px;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <h2><span>提现记录</span></h2>
            <div style="margin:20px;">
              {{--  @if($user->state ==0)
                    <a href="javascript:void(0)" onclick="popupConfirm('goAuthentication')" class="but-yes">申请提现</a>
                @elseif($user->state == 1)
                    @if(!$user->withdraw_card_number)
                        <a href="javascript:void(0)" onclick="popupConfirm('enchashAccountEdit')" class="but-yes">申请提现</a>
                        @else
                        <a href="{{url('fund/apply')}}" class="but-yes">申请提现</a>
                    @endif
                @elseif($user->state == 2)
                    <a href="javascript:void(0)" onclick="popupConfirm('goChecking')" class="but-yes">申请提现</a>
                @endif--}}
                @if(!$user->withdraw_card_number)
                	<a class="but-no" href="javascript:void(0)" onclick="popupConfirm('enchashAccountEdit')" >设置提现账户</a>
                @else
                	<a class="but-no" href="javascript:void(0)" onclick="popupConfirm('enchashAccount')" >设置提现账户</a>
                @endif
            </div>
            <table class="detailTable enchashTable">
                <tr>
                    <th>提交时间</th>
                    <th>账户余额</th>
                    <th>提现金额</th>
                    <th>提现账户信息</th>
                    <th>状态</th>
                </tr>
                @forelse($withdraws as $withdraw)
                    <td>{{$withdraw->created_at}}</td>
                    <td>{{$withdraw->balance}}</td>
                    <td>{{$withdraw->amount}}</td>
                    <td>
                        <p>{{isset($withdraw->info->withdraw_bank)?$withdraw->info->withdraw_bank:''}}</p>
                        <p>{{isset($withdraw->info->withdraw_card_number)?$withdraw->info->withdraw_card_number:''}}</p>
                        <p>{{isset($withdraw->info->withdraw_name)?$withdraw->info->withdraw_name:''}}</p>
                    </td>
                    <td>{{\App\Models\TaBilling::getStateName($withdraw->state)}}</td>
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
        <div class="title"><b>请添加提现账户</b></div>
        <div class="enchashAccountForm">
            <dl>
                <dt>账户名：</dt>
                <dd><input type="text" datatype="*" name="withdraw_name" value="{{isset($user->withdraw_name)?$user->withdraw_name:''}}" ></dd>
            </dl>
            <p class="warning">*账户名必须与签订合同的真实姓名保持一致，否则无法提现</p>
            <dl>
                <dt>银行卡号：</dt>
                <dd><input type="text" name="withdraw_card_number" maxlength="19" datatype="n16-19" nullmsg="请输入银行卡号" errormsg="请输入银行卡号" onkeyup="this.value=this.value.replace(/\D/g,'')"
                           onafterpaste="this.value=this.value.replace(/\D/g,'')"  value="{{isset($user->withdraw_card_number)?$user->withdraw_card_number:''}}"></dd>
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
                <dd><input type="text" name="withdraw_sub_bank" datatype="s1-12" nullmsg="请输入支行" errormsg="请输入支行" maxlength="12" value="{{isset($user->withdraw_sub_bank)?$user->withdraw_sub_bank:''}}"></dd>
            </dl>
            <dl>
                <dt>注册手机号：</dt>
                <dd>{{$user->mobile}}<input type="button" value="发送验证码" id="send"></dd>
            </dl>
            <dl>
                <dt>短信验证码：</dt>
                <dd><input type="text" name="mobile_code" maxlength="6" datatype="n6-6" nullmsg="请输入验证码" errormsg="请输入验证码"
                           onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"></dd>
            </dl>
        </div>
        <div class="buttonGroup">
            <input type="hidden" name="type" value="4">
            <input type="button" class="cancel" value="取消">
            <input type="submit" class="button" value="确定提交">
        </div>
        </form>
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
    </script>
    @stop
