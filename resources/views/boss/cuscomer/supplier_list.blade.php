@extends('layout')
    <style>
        #all{
            margin-left: 1%;
        }
    </style>
@section("content")
    <div id="all">
        <div>
                    <form>

                        <table class="table table-border table-bordered table-hover">
                            <tr>
                                <td>
                                    <input type="text" class="btn btn-default radius" placeholder="输入关键字" name="name" value="{{$tmp['name']}}" >
                                    <button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
                                </td>
                                <td><a class="btn btn-success" href="javascript:" onclick="a('添加供应商','/cuscomer/supplier')"><i class="Hui-iconfont">&#xe600;</i>添加供应商</a> </td>
                            </tr>
                        </table>
                    </form>
        </div>
        <div>
            <table class="table table-border table-bordered table-hover">
                <tr class="text-c">
                    <td>排名</td>
                    <td>供应商名称</td>
                    <td>店铺名称</td>
                    <td>累计销售额</td>
                    <td>在售商品数</td>
                    <td>注册时间</td>
                    <td>手机号码</td>
                    <td>操作</td>
                </tr>
                @foreach($SupplierBases as $SupplierBase)
                <tr class="text-c" id="tr_{{$SupplierBase->id}}">
                    <td style="width:20%">
                            {{$SupplierBase->id}}
                    </td>
                    <td>{{$SupplierBase->name}}</td>
                    <td>{{$SupplierBase->store_name}}</td>
                    <td>{{$SupplierBase->amount}}</td>
                    <td>{{$SupplierBase->goods_num}}</td>
                    <td>{{$SupplierBase->created_at}}</td>
                    <td>{{$SupplierBase->mobile}}</td>
                    <td>
                        <a title="查看" href="javascript:;" onclick="a( '查看', '/cuscomer/supplier/{{$SupplierBase->id}}')" class="ml-5" style="text-decoration:none">
                               查看
                        </a>
                    </td>

                </tr>
                    @endforeach

            </table>
        </div>
        <span style="float: left;line-height: 70px;margin: 0 20px">共{{$SupplierBases->lastPage()}}页,{{$SupplierBases->total()}}条数据 ；每页显示{{$SupplierBases->perPage()}}条数据</span>
        <?php echo $SupplierBases->appends(['name'=>$tmp['name']])->render();     ?>
    </div>
@endsection

@section("javascript")
    <script>
        function a(title,url){
            layer_show(title,url);
        }

        function confirm_action(message,url){
            layer.confirm(message,function(index){
                $.get(url,function (data) {
                    $("#tr_"+data).remove();
                    layer.msg('已删除!',{icon:1,time:1000});
                })
            });
        }
    </script>
@endsection