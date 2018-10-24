@extends('layout')

@section("content")
<div class="pd-20">
	<div class="text-c">
		<form class="Huiform" method="get" action="/perm/menu" target="_self">
			<input type="text" class="input-text" style="width:250px" value="{{ $display_name }}" placeholder="菜单" name="display_name">
			<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜菜单</button>
		</form>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a href="javascript:;" onclick="admin_permission_add('添加菜单','/perm/menu/create','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加菜单</a></span> <span class="r">每页：<strong>{{ $page }}</strong> 条</span> <span class="r">共有数据：<strong>{{ $menuArr['total'] }}</strong> 条；&nbsp;&nbsp;</span>  </div>
	<table class="table table-border table-bordered table-bg">
		<thead>
			<tr>
				<th scope="col" colspan="8">菜单</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type="checkbox" name="" value=""></th>
				<th width="40">ID</th>
				<th width="200">菜单昵称</th>
				<th width="60">菜单图标</th>
				<th>菜单说明</th>
				<th>所属菜单</th>
				<th width="60">排序</th>
				<th width="100">操作</th>
			</tr>
		</thead>
		<tbody>
		    @forelse($menus as $menu)
			<tr class="text-c">
				<td><input type="checkbox" value="1" name=""></td>
				<td>{{ $menu->id }}</td>
				@if($menu->parent_id == '0')
				<td>{{ $menu->display_name }}</td>
				<td><i class="Hui-iconfont">&{{ $menu->icon }}</i></td>
				@else
				<td><a title="显示该菜单权限" href="javascript:;" onclick="admin_menu_permission_list('菜单权限显示','/perm/menu/{{ $menu->id }}/permission','','510')" class="ml-5" style="text-decoration:none">{{ $menu->display_name }}</a></td>
                <td>无菜单图标</td>
				@endif
				<td>{{ $menu->description }}</td>
				<td>{{ $menu->parent_name }}</td>
				<td style="line-heigth:30px;"><div ondblclick="display_order_update(this,'/perm/menu/{{ $menu->id }}/order')" style="width:100%;height:100%;" class="display-order">{{ $menu->display_order }}</div></td>
				<td>
				    <a title="编辑" href="javascript:;" onclick="admin_permission_edit('菜单编辑','/perm/menu/{{ $menu->id }}/update','','510')" class="ml-5" style="text-decoration:none">
				        <i class="Hui-iconfont">&#xe6df;</i>
				    </a>
				    <a title="删除" href="javascript:;" onclick="table_del(this, '菜单', '/perm/menu/{{ $menu->id }}/delete')" class="ml-5" style="text-decoration:none">
				    <i class="Hui-iconfont">&#xe6e2;</i>
				    </a>
				</td>
			</tr>
			@empty
			@endforelse
		</tbody>
	</table>
	<div class="box-footer pull-right">{!! $menus->appends(['display_name'=>$display_name])->render() !!}</div>
</div>
@endsection

@section("javascript")
<script>
/*
	参数解释：
	title	标题
	url		请求的url
	id		需要操作的数据id
	w		弹出层宽度（缺省调默认值）
	h		弹出层高度（缺省调默认值）
*/
/*管理员-权限-添加*/
function admin_permission_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-权限-编辑*/
function admin_permission_edit(title,url,w,h){
	layer_show(title,url,w,h);
}

function admin_menu_permission_list(title,url,w,h){
	var index = layer.open({
		type: 2,
		title: title,
		content: url
	});
	layer.full(index);
}

</script>
@endsection