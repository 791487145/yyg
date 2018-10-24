@extends('layout_pop')

@section("content")
    <div class="pd-20">
        <form action="/cuscomer/saler" method="post" class="form form-horizontal" id="form-perm-user-add">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="row cl">
                <label class="form-label col-2"><span class="c-red">*</span>用户名称：</label>
                <div class="formControls col-6">
                    <input type="text" class="input-text" value="" placeholder="" id="user-name" name="name" datatype="*1-16" nullmsg="用户名称不能为空">
                </div>
                <div></div>
            </div>
            <div class="row cl">
                <label class="form-label col-2"><span class="c-red">*</span>手机号码：</label>
                <div class="formControls col-6">
                    <input type="text" class="input-text" value=""  id="email" name="email" >
                </div>
                <div></div>
            </div>
            <div class="row cl">
                <label class="form-label col-2"><span class="c-red">*</span>权限配置：</label>
                <div class="formControls col-6">
                    <input type="hidden"  value="{{$Roles->id}}" name="role" >
                    <input type="text" class="input-text" value="{{$Roles->name}}" placeholder="" id="password" name="role_name" readonly >
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-2"><span class="c-red"></span>用户密码：</label>
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
    </div>
@endsection

@section("javascript")
    <script>
        $(function(){
            $("#form-perm-user-add").Validform({
                tiptype:function(){},
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data == 0){
                        layer.alert('手机号已注册', {icon:2,time:5000});
                    }else{
                        parent.location.replace(parent.location.href);
                    }
                }
            });
        });
    </script>
@endsection