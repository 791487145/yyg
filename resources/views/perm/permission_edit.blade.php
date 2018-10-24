@extends('layout_pop')

@section("content")
<div class="pd-20">
	<form action="/perm/permission/{{ $perm['id'] }}/update" method="post" class="form form-horizontal" id="form-perm-permission-edit">
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red">*</span>权限路由：</label>
            <div class="formControls col-7">
                <input type="text" class="input-text" value="{{ $perm->name }}" placeholder="此处可供系统判断权限，如：/perm/route" id="menu-name" name="name" datatype="*" nullmsg="权限路由不能为空">
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red">*</span>权限昵称：</label>
            <div class="formControls col-7">
                <input type="text" class="input-text" value="{{ $perm->display_name }}" placeholder="此处为权限显示名称，如：添加、修改" id="menu-name" name="display_name" datatype="*1-16" nullmsg="权限昵称不能为空">
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red"></span>排序：</label>
            <div class="formControls col-7">
                <input type="text" class="input-text" value="{{ $perm->display_order }}" placeholder="此项可影响菜单的显示顺序" id="menu-display-order" name="display_order" datatype="n" >
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <label class="form-label col-2">权限说明：</label>
            <div class="formControls col-7">
                <input type="text" class="input-text" value="{{ $perm->description }}" placeholder="对权限的解释" id="description" name="description">
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <div class="col-10 col-offset-2">
                <button type="submit" class="btn btn-success radius" id="admin-role-save" name="admin-role-save"><i class="icon-ok"></i> 确定</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section("javascript")
<script>
$(function(){
    $("#form-perm-permission-edit").Validform({
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