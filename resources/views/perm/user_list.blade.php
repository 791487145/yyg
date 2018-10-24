@extends('layout')

@section("content")
<div class="pd-20">
	{{--<div class="text-c"> 日期范围：
    		<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;">
    		-
    		<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" id="datemax" class="input-text Wdate" style="width:120px;">
    		<input type="text" class="input-text" style="width:250px" placeholder="输入管理员名称" id="" name="">
    		<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
    	</div>--}}
    	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
    	{{--<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>--}}
    	<a href="javascript:;" onclick="admin_add('添加管理员','/perm/user/create','800','500')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加管理员</a></span> <span class="r">每页：<strong>{{ $page }}</strong> 条</span><span class="r">共有数据：<strong>{{ $userArr['total'] }}</strong> 条；&nbsp;&nbsp;</span> </div>
    	<table class="table table-border table-bordered table-bg">
    		<thead>
    			<tr>
    				<th scope="col" colspan="9">员工列表</th>
    			</tr>
    			<tr class="text-c">
    				<th width="25"><input type="checkbox" name="" value=""></th>
    				<th width="40">ID</th>
    				<th width="150">用户名</th>
    				<th width="150">邮箱（登录名）</th>
    				<th>角色</th>
    				<th width="130">加入时间</th>
    				<th width="100">操作</th>
    			</tr>
    		</thead>
    		<tbody>
    		    @forelse ($users as $user)
    			<tr class="text-c">
    				<td><input type="checkbox" value="{{ $user->id }}" name=""></td>
    				<td>{{ $user->id }}</td>
    				<td>{{ $user->name }}</td>
    				<td>{{ $user->email }}</td>
    				<td>{{ $user->role->name }}</td>
    				<td>{{ $user->created_at }}</td>
    				<td class="td-manage">
                        <a style="text-decoration:none" class="ml-5" onClick="reset_password('/perm/user/{{ $user->id }}/reset')" href="javascript:;" title="重置密码">
                            <i class="Hui-iconfont">&#xe63f;</i>
                        </a>
                        @if($user->role->level < 90)
                        <a title="编辑" href="javascript:;" onclick="admin_update('管理员编辑','/perm/user/{{ $user->id }}/update','800','500')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                        <a title="删除" href="javascript:;" onclick="table_del(this, '管理员', '/perm/user/{{ $user->id }}/delete')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6e2;</i>
                        </a>
                        @endif
                    </td>
    			</tr>
    			@empty
    			<tr class="text-c">
                	<td colspan="5">No Users</td>
                </tr>
                @endforelse
    		</tbody>
    	</table>
    	<div class="box-footer pull-right">{!! $users->render() !!}</div>
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
function admin_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-权限-编辑*/
function admin_update(title,url,w,h){
	layer_show(title,url,w,h);
}

/*重置密码*/
function reset_password(url) {
    var action = "您确定要重置该用户密码";
    var active = "重置";
    table_edit(action, active, url);
}
</script>
@endsection