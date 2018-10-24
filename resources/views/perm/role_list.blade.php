@extends('layout')

@section("content")
<div class="pd-20">
	<div class="cl pd-5 bg-1 bk-gray">
	    <span class="l">
	        <a class="btn btn-primary radius" href="javascript:;" onclick="admin_role_add('添加角色','/perm/role/create','', '')"><i class="Hui-iconfont">&#xe600;</i> 添加角色</a>
	    </span>
	    <span class="r">每页：<strong>{{ $page }}</strong> 条</span>
	    <span class="r">共有数据：<strong>{{ $roleArr['total'] }}</strong> 条；&nbsp;&nbsp;</span>
	</div>
	<table class="table table-border table-bordered table-hover table-bg">
		<thead>
			<tr>
				<th scope="col" colspan="7">角色管理</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type="checkbox" value="" name=""></th>
				<th width="40">ID</th>
				<th width="100">角色名</th>
				<th width="200">角色昵称</th>
				<th>用户列表</th>
				<th width="300">描述</th>
				<th width="70">操作</th>
			</tr>
		</thead>
		<tbody>
		    @forelse($roles as $role)
			<tr class="text-c">
				<td><input type="checkbox" value="{{ $role->id }}" name="role_id[]"></td>
				<td>{{ $role->id }}</td>
				<td>{{ $role->name }}</td>
				<td>{{ $role->display_name }}</td>
				<td>
				    @forelse($role->users as $user)
				    <a href="#">{{ $user->name }}、</a>
				    @empty
				    @endforelse
				</td>
				<td>{{ $role->description }}</td>
				<td class="f-14">
				    <a title="编辑" href="javascript:;" onclick="admin_role_edit('角色编辑','/perm/role/{{ $role->id }}/update','', '')" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
				    <a title="删除" href="javascript:;" onclick="table_del(this, '角色', '/perm/role/{{ $role->id }}/delete')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
				</td>
			</tr>
			@empty
			@endforelse
		</tbody>
	</table>
	<div class="box-footer pull-right">{!! $roles->render() !!}</div>
</div>
@endsection

@section("javascript")
<script>
/*管理员-角色-添加*/
function admin_role_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-角色-编辑*/
function admin_role_edit(title,url,w,h){
	layer_show(title,url,w,h);
}
</script>
@endsection