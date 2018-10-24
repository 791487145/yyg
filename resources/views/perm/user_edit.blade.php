@extends('layout_pop')

@section("content")
<div class="pd-20">
	<form action="/perm/user/{{ $user->id }}/update" method="post" class="form form-horizontal" id="form-perm-user-edit">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red">*</span>用户名称：</label>
            <div class="formControls col-6">
                <input type="text" class="input-text" value="{{ $user->name }}" placeholder="" id="user-name" name="name" datatype="*1-16" nullmsg="用户名称不能为空">
            </div>
            <div></div>
        </div>
        {{--<div class="row cl">--}}
            {{--<label class="form-label col-2"><span class="c-red">*</span>Email地址：</label>--}}
            {{--<div class="formControls col-6">--}}
                {{--<input type="text" class="input-text" value="{{ $user->email }}" placeholder="@" id="email" name="email" datatype="e" nullmsg="用户email不能为空">--}}
            {{--</div>--}}
            {{--<div></div>--}}
        {{--</div>--}}
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red">*</span>权限配置：</label>
            <div class="formControls col-6">
                <select name="role" class="select" id="" datatype="*" nullmsg="请选择投放区域！">
                    <option value="">请选择用户角色</option>
                    @forelse($roles as $role)
                    <option value="{{ $role->id }}" {{ $role->id == $user->role_id ? "selected" : ""}}>{{ $role->display_name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <div class="col-10 col-offset-2">
                <button type="submit" class="btn btn-success radius" id="user-save" name="route-save"><i class="icon-ok"></i> 确定</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section("javascript")
<script>
$(function(){
	$("#form-perm-user-edit").Validform({
        tiptype:2,
        ajaxPost:true,
        postonce:true,
        callback:function(data){
            if(data.ret == 'yes') {
                layer.alert(data.msg,{icon:1,time:1000});
                parent.location.replace(parent.location.href);
            } else if(data.ret == 'no') {
                layer.alert(data.msg,{icon:2,time:5000});
            } else {
                layer.alert('添加失败', {icon:2,time:5000});
            }
        }
    });
});
</script>
@endsection