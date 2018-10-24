@extends('layout')
<script type="text/javascript" src="{{asset('lib/clipboard/clipboard.min.js')}}">

</script>
<style>
    .goods-nav{font-size:14px;}
    .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
    .goods-nav .active{border-bottom: 2px solid #4395ff !important;}
    .search{margin: 20px auto;}
    .input-text{width: auto !important; margin:0px 15px;}
</style>
@section("content")
    <div class="pd-20">
        <div class="goods-nav">
            <a href="{{url('/goods/index/1')}}" class="@if($state == 1) active @endif">出售中</a>
            <a href="{{url('/goods/index/location')}}" class="@if($state == 'location') active @endif">橱窗商品</a>
            <a href="{{url('/goods/index/2')}}" class="@if($state == 2) active @endif">已下架</a>
        </div>



        <div class="search">
            <form class="Huiform" method="post" action="/goods/index/<?php echo $state?>" target="_self">

                <table class="table table-border table-bordered table-bg">
                    <thead>

                    <tr width="40%" class="text-r">
                        <td>所属地方馆:
                            <select name="pavilion_id" class="input-text">
                                <option value="0" selected >全部</option>
                                @foreach($confPavilions as $confPavilion)
                                    <option value="{{$confPavilion->id}}" @if($confPavilion->id == $pavilion_id) selected @endif>{{$confPavilion->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>商品类目:
                            <select name="category_id" class="input-text">
                                <option value="0" selected >全部</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}" @if($category->id == $category_id) selected @endif>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>橱窗位置:
                            <select name="location"  class="input-text">
                                <option value="0">请选择</option>
                                <option value="1">首页精选1</option>
                            </select>
                        </td>

                    </tr>
                    </thead>
                    <tbody>
                    <tr width="40%" class="text-r">
                        <td>供应商:
                            <select name="supplier_id"  class="input-text">
                                <option value="">请选择</option>
                                @foreach($suppliers as $key=>$val)
                                <option value="{{$key}}" @if($key == $supplier_id) selected="selected" @endif>{{$val}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>商品名称:<input type="text" class="input-text title" value="<?php echo $title?>" name="title"></td>
                        <td>总销量:<input type="text" class="input-text" value="<?php echo $num_sold_start?>" name="num_sold_start">到<input type="text" value="<?php echo $num_sold_end?>" class="input-text" name="num_sold_end"></td>
                        <td></td>
                    </tr>
                    <tr width="20%" class="text-r">
                        <td colspan="3">
                            <button type="reset" class="btn btn-danger">清空条件</button>
                            <button type="submit" class="btn btn-success" ><i class="Hui-iconfont">&#xe665;</i>搜索</button>
                            <button type="button" class="btn btn-success" onclick="exportGoods('/goods/export/<?php echo $state?>/?pavilion_id=<?php echo $pavilion_id?>&title=<?php echo $title?>&category_id=<?php echo $category_id?>&supplier_id=<?php echo $supplier_id?>&num_sold_start=<?php echo $num_sold_start?>&num_sold_end=<?php echo $num_sold_end?>')" >导出</button>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </form>
        </div>








        <table class="table table-border table-bordered table-bg">
            <thead>

            <tr class="text-c">

                <th >ID</th>
                <th >商品信息</th>
                <th >零售价（元）</th>
                <th >库存</th>
                <th >总销量</th>
                <th >实际总销量</th>
                <th >自定义销量</th>
                <th >所属馆</th>
                <th >橱窗位置</th>
                <th >分成比例</th>
                <th>位置排序</th>

                <th colspan="2">操作</th>
            </tr>
            </thead>
            <tbody>
            @forelse($goodsList as $goods)
                <tr class="text-c" id="tr_{{$goods->id}}">
                    <td class="goodsId">{{$goods->id}}</td>
                    <td><a href="{{url('goods/show',$goods->id)}}">
                        <div class="row">
                            <div class="col-md-3 text-r">
                                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods->bannerFirst['name']}}" alt="" width="60" height="60"/>
                            </div>
                            <div class="col-md-9">
                                <div class="row"><span class="c-blue"> {{$goods->title}}</span></div>
                                <div class="row"><span class="c-red">供货价:{{$goods->price_buying}}</span></div>
                            </div>

                        </div></a>
                    </td>

                    <td>{{$goods->price}}</td>
                    <td>{{$goods->num}}</td>
                    <td>{{$goods->num_sold}}</td>
                    <td>{{$goods->num_sold - $goods->num_water}}</td>
                    <td>{{$goods->num_water}}</td>
                    <td>{{$goods->pavilion}}</td>
                    <td>{{($goods->location == 1)?'首页精选':'未设橱窗'}}</td>
                    <td>
                        导游{{$goods->guide_rate}}&nbsp; 旅行社{{$goods->travel_agency_rate}}
                    </td>
                    <td><input width="30px" class="location" type="text" value="{{$goods->location_order}}"></td>
                    <td width="200" class="c-blue">
                        <div class="col-md-6">
                            @if($goods->state == 0 || $goods->state == 2)
                            <div class="row"><a href="{{url('goods/edit',$goods->id)}}" >编辑</a></div>
                                @else
                                <div class="row"><a href="{{url('goods/show',$goods->id)}}" >查看</a></div>
                            @endif
                            <div class="row"><a onclick="copy_link({{$goods->id}})" id="copy-link-{{$goods->id}}" data-clipboard-text="{{url('goods/show',$goods->id)}}">复制链接</a></div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                @if($goods->location)
                                    <a onclick="cancel_showWindow('确定取消厨窗商品吗?取消后正常售卖','{{url('/goods/action/location_cancel',$goods->id)}}')">取消厨窗商品</a>
                                @else
                                    <a onclick="dialogs('商品信息','{{url('goods/location_fix',$goods->id)}}',600,450)">设为厨窗商品</a>
                                @endif</div>
                            <div class="row">
                                @if($goods->state == 1)
                                    <a onclick="confirm_action('确定需要强制下架吗?下架后该商品将停止正常售卖','{{url('/goods/action/goods_down',$goods->id)}}')">强制下架</a>
                                @elseif($goods->state == 2)
                                    <a onclick="confirm_action('确定上架该商品吗?','{{url('/goods/action/goods_up',$goods->id)}}')">上架</a>
                                @endif

                            </div>
                        </div>




                    </td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>
        <span style="float: left;line-height: 70px;margin: 0 20px">共{{$goodsList->lastPage()}}页,{{$goodsList->total()}}条数据 ；每页显示{{$goodsList->perPage()}}条数据</span>
        <div class="page">
            <?php echo $goodsList->appends(['pavilion_id'=>$pavilion_id,'title'=>$title,'category_id'=>$category_id,'supplier_id'=>$supplier_id,'num_sold_start'=>$num_sold_start,'num_sold_end'=>$num_sold_end])->render();     ?>
        </div>
    </div>
@endsection
@section("javascript")
    <script>
        /*
         参数解释：
         title	标题
         url	请求的url
         id		需要操作的数据id
         w		弹出层宽度（缺省调默认值）
         h		弹出层高度（缺省调默认值）
         */
        /*弹窗操作*/
        function dialogs(title,url,w,h){
            layer_show(title,url,w,h);
        }


        /*弹出层操作*/
        function confirm_action(message,url){
            layer.confirm(message,function(index){
                $.get(url,function (data) {
                    if(data.msg){
                        $("#tr_"+data.id).remove();
                        layer.msg(data.msg,{icon:1,time:1000});
                    }
                })
            });
        }

        /*弹出层操作*/
        function cancel_showWindow(message,url){
            layer.confirm(message,function(index){
                $.get(url,function (data) {
                    if(data.msg){
                        layer.msg(data.msg,{icon:1,time:1000});
                        window.location.reload();
                    }
                })
            });
        }

        function copy_link(goods_id)
        {
            var clipboard = new Clipboard('#copy-link-'+goods_id);//实例化

            //复制成功执行的回调，可选
            clipboard.on('success', function(e) {
                alert('商品链接复制成功');
            });
        }

        $(".location").blur(function(){
            var location_order = $(this).val();
            var goodsId = $(this).parent().siblings(".goodsId").text();
            $.ajax({
                type:'get',
                url:'/location/',
                data:{
                    'location':location_order,
                    'goodsId':goodsId
                },
                dataType:'json',
                success:function(data){
                    alert(data);
                }
            });
        });

        function exportGoods(url){

            window.location.href=url;

        }

    </script>
@endsection
