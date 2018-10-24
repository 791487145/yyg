<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script>

            function confirm(url){
                $.post(url,function(data){
                        $("#tr_"+data).remove();

                })

            }

        </script>
    </head>
    <body>
    @if(isset($good))
    <form action="/goods/manager" method="post"><input type="text" name="title"> <input type="text"><input type="hidden" value="{{$good->state}}" name="state" id="state"><input type="text">销售量<input type="text" name="num_min" ><input type="text" name_num_max ><input type="submit"></form>
    @endif
    <ul>
            商品管理-商品库
            <li><a href="/goods/manager/{{\Illuminate\Support\Facades\Crypt::encrypt(\App\Models\GoodsBase::state_online)}} ">出售中</a></li>
            <li><a href="/goods/manager/{{\Illuminate\Support\Facades\Crypt::encrypt(\App\Models\GoodsBase::state_finish)}}">已售罄</a></li>
            <li><a href="/goods/manager/{{\Illuminate\Support\Facades\Crypt::encrypt(\App\Models\GoodsBase::state_down)}}">已下架</a></li>
        </ul>
        <table>
            <tr>
                <td>商品信息</td>
                <td>商品图</td>
                <td>名称</td>
                <td>进价</td>
                <td>赠品</td>
                <td >零售价（元）</td>
                <td>库存</td>
                <td>总销量</td>
                <td>橱窗位置</td>
                <td>操作</td>
            </tr>
            @if(isset($good))
                @if(count($good) > 0)
                    @foreach ($good as $goods)
                        <tr id="tr_{{$goods->id}}">
                            <td id>{{ $goods->id }}</td>
                            <td><img src="{{$goods->cover}}" alt=" "></td>
                            <td> {{ $goods->title }}</td>
                             <td>   {{ $goods->price_buying }}</td>
                            <td>
                                @if(isset($goods->gift_id))
                                    赠品
                                @else
                                    无
                                @endif
                            </td>
                            <td>{{ $goods->price }}</td>
                            <td>{{ $goods->num }}</td>
                            <td>{{ $goods->num_sold }}</td>
                            <td>无</td>
                            <td>
                                @if($goods->state != 2)
                                     <a href="">查看</a>

                                    <button class="button" onclick="confirm('{{url('/goods/managers/state_down',$goods->id)}}')">下架</button>
                                @endif
                                @if($goods->state == 2)
                                    <a href="">编辑</a>
                                        <button class="button" onclick="confirm('{{url('/goods/managers/state_online',$goods->id)}}')" >上架</button>

                                @endif
                            </td>
                            <td>
                                <button class="button" onclick="confirm('{{url('/goods/managers/delete',$goods->id)}}')">删除</button>
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>

                        <td><?php echo $good->render(); ?></td>

                </tr>
                <tr>
                    <td> 每页显示5条</td>
                </tr>

            @endif

        </table>


        </table>

    </body>
</html>
