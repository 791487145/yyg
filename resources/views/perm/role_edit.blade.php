@extends('layout_pop')

@section("content")
<div class="pd-20">
	<form action="/perm/role/{{ $role->id }}/update" method="post" class="form form-horizontal" id="form-perm-role-edit">
		<div class="row cl">
			<label class="form-label col-2"><span class="c-red">*</span>角色名称：</label>
			<div class="formControls col-7">
				<input type="text" class="input-text" value="{{ $role->name }}" placeholder="用来判断权限的名称" id="user-name" name="name" datatype="*" nullmsg="角色名不能为空">
			</div>
			<div></div>
		</div>
		<div class="row cl">
            <label class="form-label col-2"><span class="c-red">*</span>角色昵称：</label>
            <div class="formControls col-7">
                <input type="text" class="input-text" value="{{ $role->display_name }}" placeholder="显示给用户看的名称" id="user-name" name="display_name" datatype="*1-16" nullmsg="角色昵称不能为空">
            </div>
            <div></div>
        </div>
		<div class="row cl">
			<label class="form-label col-2">备注：</label>
			<div class="formControls col-7">
				<input type="text" class="input-text" value="{{ $role->description }}" placeholder="" id="" name="description">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-2">网站角色：</label>
			<div class="formControls col-10">
				@forelse($permissions as $permission)
                <dl class="permission-list">
                    <dt>
                        <label>
                            <input type="checkbox" id="user-Character-0">
                            {{ $permission->display_name }}</label>
                    </dt>
                    <dd>
                        @forelse($permission->son_menu as $val)
                        <dl class="cl permission-list2">
                            <dt style="overflow: hidden;">
                                <label class="">
                                    <input type="checkbox" value="" id="user-Character-0-0">
                                    {{ $val->display_name }}</label>
                            </dt>
                            <dd>
                                <label class="">
                                    <input type="checkbox" value="{{ $val->id }}" {{ in_array($val->id, $perms) ? 'checked' : ''}} name="role_check[]" id="user-Character-0-0-0">
                                    查看</label>
                                @forelse($val->son_perm as $k=>$v)
                                <label class="">
                                    <input type="checkbox" value="{{ $v->id }}" name="role_check[]" {{ in_array($v->id, $perms) ? 'checked' : ''}} id="user-Character-0-0-1">
                                    {{ $v->display_name }}</label>
                                @empty
                                @endforelse
                            </dd>
                        </dl>
                        @empty
                        <dl><dd>该菜单下无权限</dd></dl>
                        @endforelse
                    </dd>
                </dl>
                @empty
                <div>请去配置权限节点</div>
                @endforelse
			</div>
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
	$(".permission-list dt input:checkbox").click(function(){
        $(this).closest("dl").find("dd input:checkbox").prop("checked",$(this).prop("checked"));
    });
    $(".permission-list2 dd input:checkbox").click(function(){
        var l =$(this).parent().parent().find("input:checked").length;
        var l2=$(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
        if($(this).prop("checked")){
            $(this).closest("dl").find("dt input:checkbox").prop("checked",true);
            $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",true);
        }
        else{
            if(l==0){
                $(this).closest("dl").find("dt input:checkbox").prop("checked",false);
            }
            if(l2==0){
                $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",false);
            }
        }
    });

    $("#form-perm-role-edit").Validform({
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