@extends('layout')

@section("content")
<div class="pd-20">
	{{--<div class="text-c">--}}
		{{--<form class="Huiform" method="post" action="" target="_self">--}}
			{{--<input type="text" class="input-text" style="width:250px" placeholder="权限昵称" value="{{ $display_name }}" id="" name="display_name">--}}
			{{--<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜权限节点</button>--}}
		{{--</form>--}}
	{{--</div>--}}
	<div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l">
            {{--<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> --}}
            <a href="javascript:;" onclick="admin_permission_add('添加权限节点','/perm/permission/create/{{ $id }}','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加权限节点</a>
        </span>
        <span class="r">每页：<strong>{{ $page }}</strong> 条</span> <span class="r">共有数据：<strong>{{ $permissionArr['total'] }}</strong> 条；&nbsp;&nbsp;</span>
	</div>
	<table class="table table-border table-bordered table-bg">
		<thead>
			<tr>
				<th scope="col" colspan="8">所属菜单：{{ $menu_name }}</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type="checkbox" name="" value=""></th>
				<th width="40">ID</th>
                <th width="100">权限昵称</th>
                <th width="260">权限路由</th>
                <th>权限说明</th>
                <th width="60">排序</th>
                <th width="100">操作</th>
			</tr>
		</thead>
		<tbody>
			<tr class="text-c">
				<td></td>
				<td></td>
				<td>查看权限</td>
				<td></td>
				<td>菜单默认包含查看权限</td>
				<td></td>
				<td>请去菜单栏修改</td>
			</tr>
			@forelse($permissions as $permission)
			<tr class="text-c">
                <td><input type="checkbox" value="{{ $permission->id }}" name="perm_id[]"></td>
                <td>{{ $permission->id }}</td>
                <td>{{ $permission->display_name }}</td>
                <td>{{ $permission->name }}</td>
                <td>{{ $permission->description }}</td>
                <td style="line-heigth:30px;"><div ondblclick="display_order_update(this,'/perm/menu/{{ $permission->id }}/order')" style="width:100%;height:100%;" class="display-order">{{ $permission->display_order }}</div></td>
                <td>
				    <a title="编辑" href="javascript:;" onclick="admin_permission_edit('菜单编辑','/perm/permission/{{ $permission->id }}/update','','410')" class="ml-5" style="text-decoration:none">
				        <i class="Hui-iconfont">&#xe6df;</i>
				    </a>
				    <a title="删除" href="javascript:;" onclick="table_del(this, '权限', '/perm/permission/{{ $permission->id }}/delete')" class="ml-5" style="text-decoration:none">
				        <i class="Hui-iconfont">&#xe6e2;</i>
				    </a>
				</td>
            </tr>
			@empty
			@endforelse
		</tbody>
	</table>
	<div class="box-footer pull-right">{!! $permissions->render() !!}</div>
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

</script>
@endsection