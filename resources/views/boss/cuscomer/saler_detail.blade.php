@extends('layout')
    <style>
        #all{
            margin-left: 15px;
        }
        li{
            float: left;
        }
        .goods-nav .active{
            border-bottom: 2px solid #4395ff !important;
        }
        .goods-nav{font-size:14px;margin-bottom:20px;}
        .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
    </style>
@section("content")
    <div id="all">
        <div class="text-r" style="margin-top: 10px">
            <form>
                <input type="text" class="btn btn-default radius" placeholder="输入关键字" name="name" value="{{ empty($tmp['name']) ? '' : $tmp['name'] }}" >
                <button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
            </form>
        </div>
        <div style="margin-top: 10px">
            <table class="table table-border table-bordered table-hover">
                <tr>
                   <td>销售人员：{{$Users->name}}</td>
                    <td>目前排名：{{$Users->id}}</td>
                </tr>
                <tr>
                   {{-- <td>推荐旅行社数：{{$Users->ta_num}}</td>--}}
                    <td>推荐导游数：{{$Users->guide_num}}</td>
                    <td>登陆账号：{{$Users->email}}</td>
                </tr>
                <tr>
                    <td>注册时间：{{$Users->created_at}}</td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div class="goods-nav">

            {{--<a class="@if($TaBases->action == 0) active @endif" href="/cuscomer/saler/ta/{{$Users->id}}" >旅行社列表</a>--}}
            <a class="active" href="/cuscomer/saler/guide/{{$Users->id}}">导游列表</a>
        </div>
        <div>
            <table class="table table-border table-bordered table-hover">
                <tr class="text-c">
                    <td>序号</td>
                {{--    @if($TaBases->action == 0)
                        <td>名称</td>
                        <td>管辖导游数</td>
                        <td>注册地</td>
                    @else--}}
                        <td>导游姓名</td>
                        <td>导游绑定游客数</td>
                   {{-- @endif--}}
                    <td>导游累计收益</td>
                    <td>注册时间</td>
                </tr>
                @foreach($TaBases as $TaBase)
                <tr class="text-c" id="tr_{{$TaBase->id}}">
                    <td>{{$TaBase->id}}</td>
                    {{--@if($TaBases->action == 0)
                        <td>{{$TaBase->ta_name}}</td>
                        <td>{{$TaBase->guide_num}}</td>
                        @if(!empty($TaBase->province->name) && empty($TaBase->city->name))
                        <td>{{$TaBase->province->name}}</td>
                        @endif
                        @if(!empty($TaBase->province->name) && !empty($TaBase->city->name))
                            <td>{{$TaBase->province->name}}{{$TaBase->city->name}}</td>
                        @endif
                    @else--}}
                        <td>{{$TaBase->real_name}}</td>
                        <td>{{$TaBase->tour_num}}</td>
                    {{--@endif--}}
                    <td>{{$TaBase->amount}}</td>

                    <td>{{$TaBase->created_at}}</td>
                </tr>
                    @endforeach

            </table>
        </div>
        <?php echo $TaBases->render();     ?>
       <div style="margin-top: 10px" class="text-c">
           <input type="button" onclick="b()" class="input-text radius" style="width:50%" value="返回">
       </div>
    </div>
@endsection

@section("javascript")
    <script>
        function b(){
            layer_close();
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