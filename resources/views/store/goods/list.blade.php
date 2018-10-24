@extends('supplier')
@section('content')
    <style>
        .gift{left:500px !important;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <div class="statusTab">
                <span @if($state == 1) class="active" @endif><a href="{{url('/goods/lib',1)}}">出售中</a></span>
                <span @if($state == 0) class="active" @endif><a href="{{url('/goods/lib',0)}}">已售罄</a></span>
                <span @if($state == 2) class="active" @endif><a href="{{url('/goods/lib',2)}}">已下架</a></span>
            </div>
            <div class="searchForm">
                <form>
                    <table>
                        <tr>
                            <th>商品名称：</th>
                            <td><input type="text" name="goods_name" value="{{ isset($goods_name) ? $goods_name : '' }}"></td>
                            <th>商品类目：</th>
                            <td>
                                <select name="category_id">
                                    <option value="0">全部</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}" @if($category->id == $category_id = isset($category_id) ? $category_id : '')selected @endif>{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <th>所属分馆：</th>
                            <td>
                                <select name="pavilion">
                                    <option value="0">全部</option>
                                    @foreach($pavilions as $pavilion)
                                        <option value="{{$pavilion->id}}" @if($pavilion->id == $pavilion_id = isset($pavilion_id) ? $pavilion_id : '')selected @endif>{{$pavilion->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>总销量：</th>
                            <td class="inputGroup">
                                <input type="text" name="num_sold_start" value="{{ isset($num_sold_start) ? $num_sold_start : '' }}" >到<input type="text" name="num_sold_end" value="{{ isset($num_sold_end) ? $num_sold_end : '' }}">
                            </td>
                            <th>橱窗位置：</th>
                            <td>
                                <select name="location">
                                    <option value="0" @if(0 == $location_id = isset($location_id) ? $location_id : '')selected @endif>全部</option>
                                    <option value="1" @if(1 == $location_id = isset($location_id) ? $location_id : '')selected @endif>首页精选</option>
                                </select>
                            </td>
                            <th></th>
                            <td></td>
                        </tr>
                    </table>
                    <div class="buttonGroup">
                        <input type="reset" value="清空条件" class="gray">
                        <input type="submit" value="搜索">
                    </div>
                </form>
            </div>
            <div class="tabBox">
                <div class="active">
                    <table class="goodsTable">
                        <tr>
                            <th></th>
                            <th style="text-align: left;">商品信息</th>
                            <th>零售价（元）</th>
                            <th>库存</th>
                            <th>总销量</th>
                            <th>橱窗位置</th>
                            <th>操作</th>
                        </tr>
                        <tr class="allOperation">
                            <td><label><input type="checkbox" name="check1" class="allCheck">全选</label></td>
                            <td colspan="6">
                            	@if($state == 0)
                                <a href="javascript:void(0)" onclick="select_all('offSale','{{url('/goods')}}')" class="black">下架</a>
                                @endif
                                @if($state == 1)
                                <a href="javascript:void(0)" onclick="select_all('offSale','{{url('/goods')}}')" class="black">下架</a>
                                @endif
                                @if($state == 2)
                                <a href="javascript:void(0)" onclick="select_all('onSale','{{url('/goods')}}')" class="black">上架</a>
                                @endif
                                <a href="javascript:void(0)" onclick="select_all('deleteGoods','{{url('/goods')}}')" class="black">删除</a>
                            </td>
                        </tr>
                        @foreach($goodsList as $goods)
                        <tr class="goods-{{$goods->id}}">
                            <td><label><input type="checkbox" name="checkbox[]" class="checkbox" value="{{$goods->id}}">{{$goods->id}}</label></td>
                            <td><dl>
                                    <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{isset($goods->img)?$goods->img:''}}"></dt>
                                    <dd>
                                        @if($goods->gift) <span class="gift">赠品</span> @endif
                                            <a href="/goods/{{$goods->id}}"><p class="limitText">{{$goods->title}}</p></a><p>供货价：{{$goods->price_buying}}</p></dd>
                                </dl></td>
                            <td>￥{{$goods->price}}</td>
                            <td>{{$goods->num}}</td>
                            <td>{{$goods->num_sold}}</td>
                            <td>@if($goods->location == 1)默认馆首页精选 @else 无 @endif</td>
                            <td>
                                @if($goods->state ==1)
                                    <a href="/goods/{{$goods->id}}">查看</a>
                                @else
                                    <a href="/goods/{{$goods->id}}/edit">编辑</a>
                                @endif

                                @if($goods->state == 1)<a href="javascript:void(0)" onclick="patch('offSale','{{$goods->id}}')" class="black">下架</a> @endif
                                @if($goods->state == 2)<a href="javascript:void(0)" onclick="patch('onSale','{{$goods->id}}')" class="black">上架</a>  @endif
                                <a href="javascript:void(0)" onclick="patch('deleteGoods','{{$goods->id}}')" class="red">删除</a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    <div class="footPage">
                        <p>共{{$goodsList->lastPage()}}页,{{$goodsList->total()}}条数据 ；每页显示{{$goodsList->perPage()}}条数据</p>
                        <div class="pageLink">
                            {!! $goodsList->appends([
                                'goods_name' => isset($goods_name) ? $goods_name : '',
                                'category_id'=>isset($category_id) ? $category_id : 0,
                                'pavilion_id'=>isset($pavilion_id) ? $pavilion_id : 0,
                                'num_sold_start'=>isset($num_sold_start) ? $num_sold_start : '',
                                'num_sold_end'=>isset($num_sold_end) ? $num_sold_end : '',
                                'location'=>isset($location_id) ? $location_id : 0,
                                ])->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="popupBg"></div>
    <div class="popupWrap confirmWrap offSale">
        <form action="" id="offSale" method="post">
        <div class="title"><b>下架</b></div>
        <div>
            <h2>下架提示</h2>
            <p>确定需要下架吗？下架后该商品将停止正常售卖~</p>
            <div class="buttonGroup">
                <input type="button" class="cancel" value="取消">
                <input type="submit" class="submit" value="下架">
            </div>
        </div>
            <input type="hidden" name="_method" value="patch" />
            <input type="hidden" name="action" value="off" />
        </form>
    </div>
    <div class="popupWrap confirmWrap deleteGoods">
        <div class="title"><b>删除</b></div>
        <form method="post" id="deleteGoods">
        <div>
            <h2>删除提示</h2>
            <p>确定需要删除吗？删除后该商品将会被 下架并且无法找回~</p>
            <div class="buttonGroup">
                <input type="button" class="cancel" value="取消">
                <input type="submit" class="submit" value="删除">
            </div>
        </div>
        <input type="hidden" name="_method" value="patch" />
        <input type="hidden" name="action" value="delete" />
        </form>
    </div>
    <div class="popupWrap confirmWrap onSale">
        <div class="title"><b>上架</b></div>
        <form method="post" id="onSale">
        <div>
            <h2>上架提示</h2>
            <p>确定需要上架吗？上架后该商品将开始正常售卖~</p>
            <div class="buttonGroup">
                <input type="button" class="cancel" value="取消">
                <input type="submit" class="submit" value="上架">
            </div>
        </div>
        <input type="hidden" name="_method" value="patch" />
        <input type="hidden" name="action" value="on" />
        </form>
    </div>
    <script>
        //遮罩层
        function patch(box,id) {

            $(".popupBg").show();
            $("." + box).show().css("margin-top", -$(".popupWrap").height() / 2 + "px");
            $('#'+box).attr('action','/goods/'+id);
            patch_commit(box,id);
        }
        //AJAX提交
        function patch_commit(box,id){
            $(".popupBg,.popupWrap .buttonGroup .submit").click(function () {
                $(".popupBg,.popupWrap").hide();
                console.log($('.goods-'+id));
                $('.goods-'+id).remove();
                $("#"+box).Validform({
                    tiptype:2,
                    ajaxPost:true,
                    postonce:true,
                    callback:function(data){
                        if(data.ret == 'SUCCESS') {
                            $(this).parent().parent().remove();
                            layer.alert(data.msg,{icon:1,time:2000});
                            //window.location.reload();
                        }
                    }
                });
            });

        }
        function select_all(box,url){
            var ids = Array();
            $.each($('.checkbox:checked'),function(i){
                ids[i] = $(this).val();
            });
            ids = ids.join(',');
            $(".popupBg").show();
            $("." + box).show().css("margin-top", -$(".popupWrap").height() / 2 + "px");
            $('#'+box).attr('action',url+'/'+ids);
            patch_commit(box);
        }

    </script>
@stop