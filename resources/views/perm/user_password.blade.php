@extends('layout_pop')

@section("content")
<div class="pd-20">
	<div class="row cl">
        <label class="form-label col-2"><span class="c-red"></span></label>
        <div class="formControls col-6">
           {{ $user->name }}
        </div>
        <div></div>
    </div>
    @if($user->id == Auth::user()->id)
    <form action="/perm/user/{{ $user->id }}/password" method="post" class="form form-horizontal" id="form-user-pass">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red"></span>新密码：</label>
            <div class="formControls col-6">
                <input type="password" class="input-text" value="" placeholder="" id="password" name="password" datatype="*6-16" nullmsg="用户密码不能为空">
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red"></span>确认密码：</label>
            <div class="formControls col-6">
                <input type="password" class="input-text" value="" placeholder="" id="user-pass" recheck="password" name="password2" datatype="*6-16">
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <div class="col-10 col-offset-2">
                <button type="submit" class="btn btn-success radius" id="user-save" name="route-save"><i class="icon-ok"></i> 确定</button>
            </div>
        </div>
    </form>
    @else
    <div style="color:red;">
        请别闹，需要本人修改密码
    </div>
    @endif
</div>
@endsection

@section("javascript")
<script>
$(function(){
	$("#form-user-pass").Validform({
        tiptype:2,
        ajaxPost:true,
        postonce:true,
        callback:function(data){
            if(data.ret == 'yes') {
                layer.alert(data.msg,{icon:1,time:1000});
                parent.location.replace(data.url);
            } else if(data.ret == 'no') {
                layer.alert(data.msg,{icon:2,time:5000});
            } else {
                layer.alert('修改失败', {icon:2,time:5000});
            }
        }
    });
});
</script>
@endsection