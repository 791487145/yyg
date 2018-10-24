@extends('layout_pop')
@section('content')
    <style>
        .edit-label {
            width: 105px;
            display: inline-block;
        }

        .images-div {
            display: inline-block;
        }
        .desc-content{
            text-align: center;
            border: 1px solid #C2C2C2;
        }
    </style>

    <div class="row lh-30 f-20 bg-1 pd-5">商品详情</div>
    <div class="row ml-30">
        <form action="{{url('/goods/edit',$goods->id)}}" method="post">
            <!--基本信息-->
            <div class="row">
                <div class="row">
                    <p class="mt-10 f-16">基本信息</p>
                    <div class="line"></div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-4">
                        <span class="text-r edit-label">所属供应商：</span> <span>{{$store->name}}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="text-r edit-label">保证金：</span> <span>￥{{$store->deposit}}</span>
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">店铺名称：</span> <span> {{$store->store_name}}</span>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 产品分类：</span>
                        @foreach($goods->conf_categories as $category)
                            @if($category->id == $goods->category_id) {{$category->name}} @endif
                        @endforeach
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 运营分类：</span>
                    @foreach($goods->conf_categories as $conf_category)
                        <span class="category-box">
                            @if(in_array($conf_category->id,$goods->goods_category))
                                {{$conf_category->name}}
                            @endif
                        </span>
                    @endforeach
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 商品名称：</span>
                    <span>{{$goods->title}}</span>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 所属分馆：</span>
                        @foreach($goods->pavilions as $pavilion)
                            @if($pavilion->id == $goods->pavilion) {{$pavilion->name}} @endif
                        @endforeach
                </div>
                <div class="col-md-10 mt-10 photos">
                    <span class="text-r edit-label">* 封面图：</span>
                    <img src="http://{{env('IMAGE_DOMAIN')}}/{{$goods->cover}}" class="cover-upload" alt="" width="180"
                         height="120">
                    <p class="img-des">建议尺寸：700*320像素。封面图将用于商品列表等.</p>
                </div>

                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label images-label">* 轮播图：</span>
                    @foreach($goods->images as $image)
                        <div class="images-div photos">
                            <img src="{{env("IMAGE_DISPLAY_DOMAIN")}}{{$image->name}}" alt="" width="100"
                                 height="100">
                        </div>
                    @endforeach
                    <p class="ml-10 img-des">建议尺寸：800*800像素，请添加5~9张图。</p>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 重要提示：</span>
                    <span>{{$goods->ext->important_tips}}</span>
                </div>
            </div>
    <!--商品规格-->
    <div class="row">
        <div class="col-md-12">
            <div class="line"></div>
            <p class="mt-10 f-16">配送方式：

                @if(!is_null($supplierExpress))
                    <label><input name="Fruit" @if($supplierExpress->state == 1)checked="checked" @endif  disabled="disabled" type="checkbox" value="" />满{{$supplierExpress->total_amount}}元包邮,未满另加{{$supplierExpress->express_amount}}元 </label>
                @endif
                <label><input name="Fruit" @if($since) checked="checked" @endif disabled="disabled" type="checkbox" value="" />自提 </label>
            </p>
            <div class="line"></div>

        </div>
        <div class="col-md-12">
            <p class="mt-10 f-16">商品规格</p>
            <div class="line"></div>
        </div>
        @forelse($goods->spec as $k=>$spec)
            <div class="col-md-10 goods-guide mt-10 bk-gray radius">
                <div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">* 规格：</span>
                        {{$spec->name}}

                    </div>
                    <div class="col-md-5">
                        <span class="text-r edit-label">包装件数：</span>
                        {{$spec->pack_num}}
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">* 重量：</span>
                        {{$spec->weight}} 克

                    </div>
                    <div class="col-md-5">
                        <span class="text-r edit-label">* 净含量：</span>
                        {{$spec->weight_net}} 克
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">长：</span>
                        {{$spec->long}} M

                    </div>
                    <div class="col-md-5">
                        <span class="text-r edit-label">宽：</span>
                        {{$spec->wide}} M
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">高：</span>
                        {{$spec->height}} M

                    </div>
                    <div class="col-md-5">
                        <span class="text-r edit-label">* 供应价：</span>
                        {{$spec->price_buying}} 元
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">* 库存：</span>
                        {{$spec->num}} 件

                    </div>
                    <div class="col-md-5">
                        <span class="text-r edit-label">* 零售价：</span>
                        {{$spec->price}} 元
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">旅行社分成：</span>
                        {{$spec->travel_agency_rate}}%

                    </div>
                    <div class="col-md-5">
                        <span class="text-r edit-label">导游分成：</span>
                        {{$spec->guide_rate}}%
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">平台服务费：</span>
                        {{$spec->platform_fee}} 元

                    </div>
                    <div class="col-md-5">
                        <span class="text-r edit-label">* 原价： </span>
                        {{$spec->price_market}} 元
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">自定义销量：</span>
                        <input type="text" class="input-text radius" datatype="n" style="width:30%" value="{{$spec->num_water}}" id="num_water_{{$spec->id}}"  onblur="edit({{$spec->id}})"> 件
                    </div>
                    <div class="col-md-5">
                        <span class="text-r edit-label">限购数量：{{$spec->num_limit}}件</span>
                    </div>
                </div>
                {{--<div class="col-md-10 mt-10">
                    <div class="col-md-5">
                        <span class="text-r edit-label">运费：</span>
                        <input type="checkbox" name="express_fee_mode" @if(1 == $express_fee_mode = isset($spec->express_fee_mode)?$spec->express_fee_mode:'') checked @endif/>全国包邮

                    </div>
                    <div class="col-md-5">
                        <input type="checkbox" name="is_pick_up" @if(1 == $is_pick_up = isset($spec->is_pick_up)?$spec->is_pick_up:'') checked @endif/>是否允许自提
                    </div>
                </div>--}}
            </div>
            @empty
        @endforelse
    </div>
    <!--基本属性-->
    <div class="row">
        <div class="col-md-12">
            <p class="mt-10 f-16">基本属性</p>
            <div class="line"></div>
        </div>

        <div class="col-md-10 pd-10 mt-10">
            <div class="col-md-10 mt-10">
                <div class="col-md-5">
                    <span class="text-r edit-label">* 发货地：</span>
                    {{$goods->ext->send_out_address}}

                </div>
                <div class="col-md-5">
                    <span class="text-r edit-label">* 产地：</span>
                    {{$goods->ext->product_area}}
                </div>
            </div>
            <div class="col-md-10 mt-10">
                <div class="col-md-5">
                    <span class="text-r edit-label">* 保质期：</span>
                    {{$goods->ext->shelf_life}}

                </div>
                <div class="col-md-5">
                    <span class="text-r edit-label">* 贮藏：</span>
                    {{$goods->ext->store}}
                </div>
            </div>
            <div class="col-md-10 mt-10">
                <div class="col-md-5">
                    <span class="text-r edit-label">* 包装：</span>
                    {{$goods->ext->pack}}

                </div>
                <div class="col-md-5">
                    <span class="text-r edit-label">* 快递说明：</span>
                    {{$goods->ext->express_desc}}
                </div>
            </div>
            <div class="col-md-10 mt-10">
                <div class="col-md-5">
                    <span class="text-r edit-label">* 发货说明：</span>
                    {{$goods->ext->send_out_desc}}

                </div>
                <div class="col-md-5">
                    <span class="text-r edit-label">* 售后说明：</span>
                    {{$goods->ext->sold_desc}}
                </div>
            </div>
            <div class="col-md-10 mt-10">
                <div class="col-md-5">
                    <span class="text-r edit-label">食品添加剂：</span>
                    {{$goods->ext->food_addiitive}}

                </div>
                <div class="col-md-5">
                    <span class="text-r edit-label">生产许可证：</span>
                    {{$goods->ext->product_license}}
                </div>
            </div>
            <div class="col-md-10 mt-10">
                <div class="col-md-5">
                    <span class="text-r edit-label">等级：</span>
                    {{$goods->ext->level}}

                </div>
                <div class="col-md-5">
                    <span class="text-r edit-label">制造产商/公司：</span>
                    {{$goods->ext->company}}
                </div>
            </div>
            <div class="col-md-10 mt-10">
                <div class="col-md-5">
                    <span class="text-r edit-label">配料表：</span>
                    <span>{{$goods->ext->food_burden}}</span>

                </div>
                <div class="col-md-5">
                    <div class="row">
                        <span class="text-r edit-label">经销商：</span>
                        {{$goods->ext->dealer}}
                    </div>
                    <div class="row mt-10">
                        <span class="text-r edit-label">地址：</span>
                        {{$goods->ext->address}}
                    </div>
                    <div class="row mt-10">
                        <span class="text-r edit-label">特别说明：</span>
                        {{$goods->ext->remark}}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--赠品信息-->
    <div class="row">
        <div class="col-md-12">
            <p class="mt-10 f-16">赠品信息</p>
            <div class="line"></div>
        </div>
        @foreach($goods->gift as $gift)
            <div class="col-md-10 mt-10">
                <div class="col-md-1">
                    {{$gift->gift_id}}
                </div>
                <div class="col-md-1">
                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$gift->data->cover}}" alt="" width="80" height="80"/>
                </div>
                <div class="col-md-4 ml-10">
                    {{$gift->data->title}}
                </div>
                <div class="col-md-2">
                    规格：{{$gift->spec->name}}
                </div>
                <div class="col-md-2">
                    库存：{{$gift->data->num}}件
                </div>

            </div>
        @endforeach
    </div>
    <!--商品详情-->
    <div class="row">
        <div class="col-md-12">
            <p class="mt-10 f-16">商品详情</p>
            <div class="line"></div>
        </div>
        <div class="col-md-10 mt-10">
            <div class="mt-10 desc-content">
                {!! $goods->ext->description !!}
            </div>
        </div>
    </div>
    <div class="col-md-10 text-c pd-20">
        {{--@if($goods->state == 0)--}}
            {{--<button type="button" onclick="accept()" class="btn btn-success mr-30">审核通过</button>--}}
            {{--<button type="button" onclick="dialogs('审核驳回','{{url('/goods/refute',$goods->id)}}',720,370)" class="btn btn-danger">审核不通过</button>--}}
        {{--@elseif($goods->state == 3)--}}
            {{--<span class="btn mr-30">审核已驳回</span>--}}
            {{--<a href="{{url('/goods/check')}}" class="btn btn-danger">返回</a>--}}
        {{--@else--}}
            {{--<span class="btn mr-30">审核已通过</span>--}}
            {{--<a href="{{url('/goods/check')}}" class="btn btn-danger">返回</a>--}}
        {{--@endif--}}
            <a href="javascript:window.history.back()" class="btn btn-danger">返回</a>
    </div>
    </form>
    </div>
    </div>
@stop
@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>
        layer.photos({
            photos: '.photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });
        /*弹窗操作*/
        function dialogs(title,url,w,h){
            layer_show(title,url,w,h);
        }

        function edit(obj){
            var val = $("#num_water_"+obj).val();
            $.post("/goods/numsold/edit",{specId:obj,num_water:val},function(data){
                layer.msg(data.msg,{icon:1,time:1000});
            })
        }
    </script>
@stop
