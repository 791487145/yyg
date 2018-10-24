@extends('layout_pop')

@section("content")
    <div class="pd-20">
        <form action="{{url('goods/location_fix')}}" method="post" class="form form-horizontal" id="form-perm-permission-edit">
            <table class="table table-border table-bordered table-bg">
                <tr>
                    <td class="text-c">
                        <img src="{{asset('images/user.png')}}" alt="">
                    </td>
                    <td>
                        <p>ID</p>
                        <P>{{$goods->id}}</P>
                        <input type="hidden" name="id" value="{{$goods->id}}">
                    </td>
                    <td>
                        <p>商品名称</p>
                        <p>{{$goods->title}}</p>
                    </td>
                </tr>
                <tr>
                    <td>

                        
                        推荐至
                    </td>
                    <td colspan="2" class="text-l">
                        <select class="input-text" name="pavilion">
                            @foreach($pavilions as $pavilion)
                                <option value="{{$pavilion->id}}" selected>{{$pavilion->name}}</option>
                            @endforeach
                        </select>
                    </td>

                </tr>
                <tr>
                    <td>
                        商品排序
                    </td>
                    <td colspan="2">
                        <input type="text" class="input-text" name="location_order" />
                    </td>

                </tr>
            </table>
            <div class="row">
                <div class="row">

                    <button type="submit" class="mr-30 btn btn-success radius" id="admin-role-save" name="admin-role-save"><i class="icon-ok"></i> 确    定</button>
                    <button type="button" class="btn btn-danger radius" onclick="layer_close();">取      消</button>

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