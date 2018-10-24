@extends('layout_pop')

@section("content")
<div class="pd-20">
	<form action="/perm/menu/create" method="post" class="form form-horizontal" id="form-perm-menu-add">
	    <input type="hidden" value="1" name="is_menu"/>
		<div class="row cl">
			<label class="form-label col-2"><span class="c-red">*</span>菜单路由：</label>
			<div class="formControls col-7">
				<input type="text" class="input-text" value="" placeholder="此处可供系统判断权限" id="menu-name" name="name" datatype="*" nullmsg="菜单路由不能为空">
			</div>
			<div></div>
		</div>
		<div class="row cl">
            <label class="form-label col-2"><span class="c-red">*</span>菜单昵称：</label>
            <div class="formControls col-7">
                <input type="text" class="input-text" value="" placeholder="此处为菜单显示名称" id="menu-name" name="display_name" datatype="*1-16" nullmsg="菜单昵称不能为空">
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red"></span>菜单图标：</label>
            <div class="formControls col-7">
                <input type="text" class="input-text" value="" placeholder="此项为菜单的显示图标，请去掉&" id="menu-icon" name="icon" >
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red"></span>排序：</label>
            <div class="formControls col-7">
                <input type="text" class="input-text" value="0" placeholder="此项可影响菜单的显示顺序" id="menu-display-order" name="display_order" datatype="n" >
            </div>
            <div></div>
        </div>
        <div class="row cl">
            <label class="form-label col-2"><span class="c-red">*</span>所属菜单：</label>
            <div class="formControls col-7">
                <select name="parent_id" class="select" id="">
                    <option value="0">一级菜单</option>
                    @forelse($parent_menus as $menu)
                    <option value="{{ $menu->id }}">{{ $menu->display_name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div></div>
        </div>
		<div class="row cl">
			<label class="form-label col-2">菜单说明：</label>
			<div class="formControls col-7">
				<input type="text" class="input-text" value="" placeholder="二级菜单默认会拥有查看权限" id="description" name="description">
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
	$("#form-perm-menu-add").Validform({
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