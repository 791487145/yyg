@extends('layout_pop')
@section('content')
    <style>
        .edit-label {
            width: 105px;
            display: inline-block;
        }

        .img-des, .important-des {
            margin-left: 105px;
        }

        .important-des {
            position: relative;
            top: -18px;
        }

        .category-box {
            display: inline-block;
            width: 80px;
        }

        .goods-guide {

        }

        .goods-edit-input {
            width: 220px !important;
        }

        /*文件上传样式重写*/

        .uploadify {
            position: relative;
            left: 109px;
            margin-top: 10px;
            border: 1px solid #c0c0c0;
        }

        .uploadify-queue {
            display: none;
        }

        .uploadify-button-text {
            line-height: 45px;
            font-size: 50px;
            text-align: center;
            display: inline-block;
            width: 50px;
            height: 50px;
            color: #c0c0c0;
        }

        .img-del {
            display: inline-block;
            width: 13px;
            height: 13px;
            position: relative;
            top: -41px;
            left: -13px;
            cursor: pointer;
            background: url("{{asset('images/acrossTab-close.png')}}") -91px -13px;
        }
        .radio{cursor: pointer;display: inline-block;position: absolute;left: 0px;top:100px;}
        .images-div {
            display: inline-block;
            position: relative;padding-bottom:30px;background:none;
        }

        .des-upload > .uploadify {
            left: 0 !important;
        }
        .desc-del {
            display: inline-block;
            width: 19px;
            height: 19px;
            position: relative;
            top: -378px;
            left: 182px;
            cursor: pointer;
            background: url("{{asset('images/icon_error_s.png')}}");
        }
        .desc-add{
            line-height: 40px !important;
            font-size: 50px;
            text-align: center;
            display: inline-block;
            width: 50px;
            height: 50px;
            color: #c0c0c0;
            padding:0;
            background: #fff;
        }
        .spec-del{
            display: inline-block;
            width: 19px;
            height: 19px;
            position: relative;
            top: 0px;
            left: 214px;
            cursor: pointer;
            background: url("{{asset('images/icon_error_s.png')}}");
        }
    </style>

    <div class="row lh-30 f-20 bg-1 pd-5">商品编辑</div>
    <div class="row ml-30">
        <form action="{{url('/goods/edit',$goods->id)}}" method="post" id="goods-edit">
            <!--基本信息-->
            <div class="row">
                <div class="row">
                    <p class="mt-10 f-16">基本信息</p>
                    <div class="line"></div>
                </div>
                <div class="col-md-10 mt-10">
                    <div class="col-md-4">
                        <span class="text-r edit-label">所属供应商：</span> <span>{{isset($store->name)?$store->name:''}}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="text-r edit-label">保证金：</span> <span>￥{{isset($store->deposit)?$store->deposit:0.00}}</span>
                    </div>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">店铺名称：</span> <span>{{isset($store->store_name)?$store->store_name:''}}</span>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 产品分类：</span>
                    <select name="category_id">
                        @foreach($goods->conf_categories as $category)
                            <option value="{{$category->id}}"
                                    @if($category->id == $goods->category_id) selected @endif>{{$category->name}}</option>

                        @endforeach
                    </select>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 运营分类：</span>
                    @foreach($goods->conf_categories as $conf_category)
                        <span class="category-box"><input type="checkbox"
                                                          @if(in_array($conf_category->id,$goods->goods_category)) checked
                                                          @endif name="goods_category[]"
                                                          value="{{$conf_category->id}}">{{$conf_category->name}}</span>

                    @endforeach
                    (可多选)
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 商品名称：</span>
                    <input type="text" class="input-text goods-edit-input" name="title" value="{{$goods->title}}"/>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 所属分馆：</span>
                    <select name="pavilion">
                        @foreach($goods->pavilions as $pavilion)
                            <option value="{{$pavilion->id}}"
                                    @if($pavilion->id == $goods->pavilion) selected @endif >{{$pavilion->name}}</option>
                        @endforeach
                    </select>
                    (可修改)
                </div>
                <div class="col-md-10 mt-10 photos">

                    <span class="text-r edit-label">* 封面图：</span>
                    <img src="http://{{env('IMAGE_DOMAIN')}}/{{$goods->cover}}" class="cover-upload" alt="" width="180"
                         height="120">
                    <input type="hidden" name="cover" class="cover" value="{{$goods->cover}}"/>
                    <input type="file" id="cover-upload"/>
                    <p class="img-des">建议尺寸：700*320像素。封面图将用于商品列表等.</p>
                </div>

                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label images-label">* 轮播图：</span>
                    <span class="fileResultbox">
                    @forelse($goods->images as $key => $image)
                        <div class="images-div photos" data-id="{{$image->id}}">
                            <div class="radio"><input type="radio" name="img" value="{{env("IMAGE_DISPLAY_DOMAIN")}}/{{$image->name}}" @if($key == 0)checked="checked"@endif>设置为封面</div>
                            <img src="{{env("IMAGE_DISPLAY_DOMAIN")}}/{{$image->name}}" alt="{{$image->name}}" width="100" height="100">
                            <span class="img-del"></span>
                            <input type="hidden" name="image_add[]" value="{{$image->name}}">
                        </div>
                        @empty
                    @endforelse
                    </span>
                    <input type="hidden" name="image_del" class="image_del" />
                    <input type="file" id="images-file-upload"/>
                    <p class="ml-10 img-des">建议尺寸：800*800像素，请添加5~9张图。</p>
                </div>
                <div class="col-md-10 mt-10">
                    <span class="text-r edit-label">* 重要提示：</span>
                    <div>
                        <textarea name="important_tips" class="important-des text-l" cols="60"
                                  rows="5">{{$goods->ext->important_tips}}</textarea>
                    </div>
                </div>
            </div>
            <!--商品规格-->
            <div class="row">
                <div class="col-md-12">
                    <p class="mt-10 f-16">配送方式：

                        @if(!is_null($supplierExpress))
                            <label><input name="Fruit" @if($supplierExpress->state == 1)checked="checked" @endif disabled="disabled" type="checkbox" value="" />满{{$supplierExpress->total_amount}}元包邮,未满另加{{$supplierExpress->express_amount}}元 </label>
                        @endif
                            <label><input name="Fruit" @if($since) checked="checked" @endif disabled="disabled" type="checkbox" value="" />自提 </label>

                    </p>
                    <div class="line"></div>

                </div>
                <div class="col-md-12">
                    <p class="mt-10 f-16">商品规格</p>
                    <div class="line"></div>
                </div>

                @foreach($goods->spec as $k=>$spec)
                <div class="col-md-10 goods-guide mt-10 bk-gray radius">
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 规格：</span>
                            <input type="text" name="name[]" class="input-text goods-edit-input" value="{{$spec->name}}"/>

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">包装件数：</span>
                            <input type="text" name="pack_num[]" class="input-text goods-edit-input" value="{{$spec->pack_num}}"/>
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 重量：</span>
                            <input type="text" name="weight[]" class="input-text goods-edit-input" value="{{$spec->weight}}"/> 克

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 净含量：</span>
                            <input type="text" name="weight_net[]" class="input-text goods-edit-input" value="{{$spec->weight_net}}"/> 克
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">长：</span>
                            <input type="text" name="long[]" class="input-text goods-edit-input" value="{{$spec->long}}"/> M

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">宽：</span>
                            <input type="text" name="wide[]" class="input-text goods-edit-input" value="{{$spec->wide}}"/> M
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">高：</span>
                            <input type="text" name="height[]" class="input-text goods-edit-input" value="{{$spec->height}}"/> M

                        </div>
                        <div class="col-md-5 price_buying-div">
                            <span class="text-r edit-label">* 供应价：</span>
                            <input type="text" name="price_buying[]" class="input-text goods-edit-input price_buying" value="{{$spec->price_buying}}"/> 元
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 库存：</span>
                            <input type="text" name="num[]" class="input-text goods-edit-input" value="{{$spec->num}}"/> 件

                        </div>
                        <div class="col-md-5 price-div">
                            <span class="text-r edit-label">* 零售价：</span>
                            <input type="text" name="price[]" class="input-text goods-edit-input price" value="{{$spec->price}}"/> 元
                        </div>
                    </div>

                    <div class="col-md-10 mt-10">
                        <div class="col-md-5 travel">
                            <span class="text-r edit-label">旅行社分成：</span>
                            <input type="text" name="travel_agency_rate[]" class="input-text goods-edit-input travel_agency_rate" value="{{$spec->travel_agency_rate}}"/> 按百分比
                        </div>
                        <div class="col-md-5 guide">
                            <span class="text-r edit-label">导游分成：</span>
                            <input type="text" name="guide_rate[]" class="input-text goods-edit-input guide_rate" value="{{$spec->guide_rate}}"/> 按百分比
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">平台服务费：</span>
                            <input type="hidden" name="platform_fee[]" class="platform_fee_input" value="{{$spec->platform_fee}}"/>
                            <input type="text" class="input-text goods-edit-input platform_fee servercharge" value="{{$spec->platform_fee}}" disabled/> 元

                        </div>
                        <div class="col-md-5 price-div">
                            <span class="text-r edit-label">* 原价：</span>
                            <input type="text" name="price_market[]" class="input-text goods-edit-input price" value="{{$spec->price_market}}"/> 元
                        </div>
                    </div>

                    <div class="col-md-10 mt-10">
                        <div class="col-md-5 price-div">
                            <span class="text-r edit-label">购买限制：</span>
                            <input type="text" name="num_limit[]" class="input-text goods-edit-input price" value="{{$spec->num_limit}}"/> 件
                        </div>
                    </div>
                    {{--<div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">运费：</span>
                            <input type="hidden" name="express_fee_mode[]" value="{{$spec->express_fee_mode}}">
                            <input type="checkbox" class="express_fee_mode" @if($spec->express_fee_mode == 1) checked @endif/>全国包邮

                        </div>
                        <div class="col-md-5">
                            <input type="hidden" name="is_pick_up[]" value="{{$spec->is_pick_up}}">
                            <input type="checkbox" class="is_pick_up" @if($spec->is_pick_up == 1) checked @endif />是否允许自提
                        </div>
                    </div>--}}
                    @if($k > 0)<span class="spec-del" onclick="spec_remove(this)" data-id="{{$spec->id}}"></span>@endif
                    <input type="hidden" name="spec_id[]" value="{{$spec->id}}" />
                </div>
                @endforeach
                <div class="col-md-10 mt-10 spec-add">
                    <div class="col-md-10 mt-10 gift-btn">
                        <a class="btn btn-default" onclick="guide_add()">添加规格</a>
                    </div>
                </div>
                <input type="hidden" name="spec_del" class="spec_del" />
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
                            <input type="text" name="send_out_address" class="input-text goods-edit-input" value="{{$goods->ext->send_out_address}}"/>

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 产地：</span>
                            <input type="text" name="product_area" class="input-text goods-edit-input" value="{{$goods->ext->product_area}}"/>
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 保质期：</span>
                            <input type="text" name="shelf_life" class="input-text goods-edit-input" value="{{$goods->ext->shelf_life}}"/>

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 贮藏：</span>
                            <input type="text" name="store" class="input-text goods-edit-input" value="{{$goods->ext->store}}"/>
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 包装：</span>
                            <input type="text" name="pack" class="input-text goods-edit-input" value="{{$goods->ext->pack}}"/>

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 快递说明：</span>
                            <input type="text" name="express_desc" class="input-text goods-edit-input" value="{{$goods->ext->express_desc}}"/>
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 发货说明：</span>
                            <input type="text" name="send_out_desc" class="input-text goods-edit-input" value="{{$goods->ext->send_out_desc}}"/>

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">* 售后说明：</span>
                            <input type="text" name="sold_desc" class="input-text goods-edit-input" value="{{$goods->ext->sold_desc}}"/>
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">食品添加剂：</span>
                            <input type="text" name="food_addiitive" class="input-text goods-edit-input" value="{{$goods->ext->food_addiitive}}"/>

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">生产许可证：</span>
                            <input type="text" name="product_license" class="input-text goods-edit-input" value="{{$goods->ext->product_license}}"/>
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">等级：</span>
                            <input type="text" name="level" class="input-text goods-edit-input" value="{{$goods->ext->level}}"/>

                        </div>
                        <div class="col-md-5">
                            <span class="text-r edit-label">制造产商/公司：</span>
                            <input type="text" name="company" class="input-text goods-edit-input" value="{{$goods->ext->company}}"/>
                        </div>
                    </div>
                    <div class="col-md-10 mt-10">
                        <div class="col-md-5">
                            <span class="text-r edit-label">配料表：</span>
                            <textarea name="food_burden" class="food_burden" cols="60" rows="6">{{$goods->ext->food_burden}}</textarea>

                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <span class="text-r edit-label">经销商：</span>
                                <input type="text" name="dealer" class="input-text goods-edit-input" value="{{$goods->ext->dealer}}"/>
                            </div>
                            <div class="row mt-10">
                                <span class="text-r edit-label">地址：</span>
                                <input type="text" name="address" class="input-text goods-edit-input" value="{{$goods->ext->address}}"/>
                            </div>
                            <div class="row mt-10">
                                <span class="text-r edit-label">特别说明：</span>
                                <input type="text" name="remark" class="input-text goods-edit-input" value="{{$goods->ext->remark}}"/>
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
                @forelse($goods->gift as $gift)
                <div class="col-md-10 mt-10" data-id="{{isset($gift->gift_id)?$gift->gift_id:0}}">
                        <div class="col-md-1">
                            {{isset($gift->gift_id)?$gift->gift_id:0}}
                        </div>
                        <div class="col-md-1 photos">
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{isset($gift->data->cover)?$gift->data->cover:''}}" alt="" width="80" height="80"/>
                        </div>
                        <div class="col-md-4">
                            {{isset($gift->data->title)?$gift->data->title:''}}
                        </div>
                        <div class="col-md-2">
                            规格：{{isset($gift->spec->name)?$gift->spec->name:''}}
                        </div>
                        <div class="col-md-2">
                            库存：{{isset($gift->data->num)?$gift->data->num:0}}   件
                        </div>
                        <div class="col-md-2 "><a class="c-blue gift-del">删除</a></div>
                </div>
                    @empty
                @endforelse
                <div class="col-md-10 mt-10">

                    {{--<div class="col-md-10 mt-10 gift-add_btn">
                        <a  class="btn btn-default" onclick="dialogs('添加赠品','{{url('gift/add')}}',720,370)">添加赠品</a>
                    </div>--}}
                    <input type="hidden" name="gift_add" class="gift_add" />
                    <input type="hidden" name="gift_del" class="gift_del" />
                </div>
            </div>

            <!--商品详情-->
            <div class="row">
                <div class="col-md-12">
                    <p class="mt-10 f-16">商品详情</p>
                    <div class="line"></div>
                </div>

                <div class="col-md-10 mt-10">

                    <div class="col-md-10 mt-10 desc-content">
                        <script type="text/plain" id="myEditor" style="width:1000px;height:240px;">{!! $goods->ext->description !!}</script>
                    </div>
                </div>
            </div>
            <div class="col-md-8 text-c pd-20">
                 <!-- 商品审核状态 -->
                            @if($goods->state == 0)
                            <input type="hidden" name="goods_accept" value="1">
                        <button type="submit" class="btn btn-success mr-30 auditPass">审核通过</button>
                        <button type="button" onclick="dialogs('审核驳回','{{url('/goods/refute',$goods->id)}}',720,370)" class="btn btn-danger mr-30">审核不通过</button>
                        @else
                        <button type="submit" class="btn btn-success">保存并更新</button>
                            @endif
                        <a href="javascript:window.history.back()" class="btn btn-danger mr-30">返回</a>

            </div>
        </form>
    </div>
    </div>
    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('lib/umeditor/third-party/template.min.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('lib/umeditor/umeditor.config.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('lib/umeditor/umeditor.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('lib/umeditor/lang/zh-cn/zh-cn.js') }}"></script>
    <link href="{{ asset('lib/umeditor/themes/default/css/umeditor.css') }}" type="text/css" rel="stylesheet">
    <script type="text/javascript">
        //实例化编辑器
        var um = UM.getEditor('myEditor');
        <?php $timestamp = time();?>
        $(function () {
            var goods_state = '{{$goods->state}}';
            $("#goods-edit").Validform({
                tiptype:function(){},
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'yes') {
                        layer.open({
                            content: data.msg,
                            btn: ['确认'],
                            yes: function(index, layero) {
                                if (goods_state == 0){
                                    window.location.href= '{{url('goods/check/0')}}';
                                }else{
                                    window.location.href= '{{url('goods/index/2')}}';
                                }

                            },
                            cancel: function() {
                                if (goods_state == 0){
                                    window.location.href= '{{url('goods/check/0')}}';
                                }else{
                                    window.location.href= '{{url('goods/index/2')}}';
                                }
                            }
                        });
                    } else {
                        layer.alert(data.msg, {icon:2,time:5000});
                    }
                }
            });
            //轮播图上传
            $('#images-file-upload').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/goods/upload') }}',
                'buttonText': '+',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 50,
                'width': 50,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    $("body").append('<img  id="testimg" src="" style="display:none">')
                    $('#testimg').attr('src',"{{env('IMAGE_DISPLAY_DOMAIN')}}/"+data.img);                                                        
                    $('#testimg').one('load',function() {
                        var imgWidth = this.width;
                        var imgHeight = this.height;
                        if (imgWidth !== imgHeight ) {
                            layer.alert('请上传1:1尺寸图片');
                        }else{
                        	var legth = $(".images-div");
		                    if(legth.length<9){
		                        $("#images-file-upload").show();
		                        var images_div = '<div class="images-div photos">' +
		                                '<div class="radio"><input type="radio" name="img" value="{{env('IMAGE_DISPLAY_DOMAIN')}}/'+data.img+'"/>设置为封面</div>'+
		                                '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}/' + data.img + '" alt="" width="100" height="100"><span class="img-del"></span>' +
		                                '<input type="hidden" name="image_add[]" value="' + data.img + '"></div>';
		                        $('.fileResultbox').append(images_div);
		
		                        $('.image_add').remove();
		                        if($('.upload-img').length = 1){
		                            $('input[type="radio"]').eq(0).attr("checked","checked");
		                        }
		                        if(legth.length>7){
		                            $("#images-file-upload").hide();
		                        }
		                    }else{
		                        $("#images-file-upload").hide();
		                    }
		                    //轮播图删除
		                    images_remove();
		                    //选择封面
		                    selectCover ();
                        }
                    });
                }
            });
            //选择封面
            selectCover ();
            function selectCover (){
                $('.radio input').click(function(){
                    var ishide = $(this).parent().parent(".images-div").find("input[name=image_select]").val();
                    if(ishide){
                        $(this).parents(".images-div").prependTo(".fileResultbox");
                    }else {
                        var imgUrl = $(this).parents(".images-div").find("img").attr("alt");
                        var html = '<input type="hidden" name="image_select[]" value="'+imgUrl+'">'

                        $(this).parents(".images-div").append(html);
                        $(this).parents(".images-div").prependTo(".fileResultbox");
                    }
                })
            }
            //封面图上传
            $('#cover-upload').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/goods/upload') }}',
                'buttonText': '+',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 50,
                'width': 50,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    $('.cover-upload').attr('src', '{{env("IMAGE_DISPLAY_DOMAIN")}}/' + data.img);
                    $('.cover').val(data.img);
                }
            });

            //轮播图删除
            function images_remove() {
                $('.img-del').bind('click', function () {
                    var image_id = $(this).parent().attr('data-id');
                    if(image_id > 0){
                        $(this).parent().parent().append('<input type="hidden" name="image_del[]" value="'+image_id+'">');
                        $('.image_del').remove();
                    }
                    $(this).parent().remove();
                    var legth = $(".images-div");
                    if(legth.length>8){
                        $("#images-file-upload").hide();
                    }else{
                        $("#images-file-upload").show();
                    }
                })
            }
            images_remove();
            gift_remove();
            platform_fee();
        });
        function express_handle(){
            $('.express_fee_mode').click(function(){
                var flag = $(this).prop("checked");
                if (flag){
                    $(this).prev().val(1);
                }else{
                    $(this).prev().val(2);
                }
            });
        }
        function pick_handle(){
            $('.is_pick_up').click(function(){
                var flag = $(this).prop("checked");
                if (flag){
                    $(this).prev().val(1);
                }else{
                    $(this).prev().val(2);
                }
            });
        }
        express_handle();
        pick_handle();
    function platform_fee(){
        $('.travel_agency_rate,.price,.price_buying,.guide_rate').blur(function(){
            //零售价 - (供应价 + 零售价 * (导游分成 + 旅行社分成))
            var price = $(this).parent().parent().parent('.goods-guide').find('.price').val();
            var price_buying = $(this).parent().parent().parent('.goods-guide').find('.price_buying').val();
            var travel = $(this).parent().parent().parent('.goods-guide').find('.travel_agency_rate').val();
            var guide = $(this).parent().parent().parent('.goods-guide').find('.guide_rate').val();
            var number = Number(price)-(Number(price_buying)+Number(price)*(Number(travel)+Number(guide))/100);
            number = Math.floor(number * 100) / 100;
            $(this).parent().parent().parent('.goods-guide').find('.platform_fee').val(number);
            $(this).parent().parent().parent('.goods-guide').find('.platform_fee_input').val(number);
        });
    }
        function spac_platform_fee(){
            $('.spec_travel_agency_rate,.spec_price,.spec_price_buying,.spec_guide_rate').blur(function(){
                //零售价 - (供应价 + 零售价 * (导游分成 + 旅行社分成))
                var price = $(this).parent().parent().parent().find('.spec_price').val();
                var price_buying = $(this).parent().parent().parent().find('.spec_price_buying').val();
                var travel = $(this).parent().parent().parent().find('.spec_travel_agency_rate').val();
                var guide = $(this).parent().parent().parent().find('.spec_guide_rate').val();
                var number = Number(price)-(Number(price_buying)+Number(price)*(Number(travel)+Number(guide))/100);
                number = Math.floor(number * 100) / 100;
                $(this).parent().parent().parent().find('.spec_platform_fee').val(number);
                $(this).parent().parent().parent().find('.spec_platform_fee_input').val(number);
            });
        }

    </script>
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

        function gift_remove(){
            $('.gift-del').bind('click',function(){
                var gift_id = $(this).parent().parent().attr('data-id');
                if(gift_id > 0){
                    $(this).parent().parent().parent().append('<input type="hidden" name="gift_del[]" value="'+gift_id+'">');
                    $('.gift_del').remove();
                }
                $(this).parent().parent().remove();
            });
        }
        //规格删除
        function spec_remove(obj){
            var spec_id = $(obj).attr('data-id');
            $(obj).parent().remove();
            $('.spec_del').remove();
            if(spec_id > 0){
                $('.spec-add').after('<input type="hidden" name="spec_del[]" value="'+spec_id+'">');
            }


        }
        function guide_add(){
            var html='<div class="col-md-10 mt-10 bk-gray radius">'+
            '<div class="col-md-10 mt-10">'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">* 规格：</span>'+
            '<input type="text" name="spec_name[]" class="input-text goods-edit-input" />'+
            '</div>'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">包装件数：</span>'+
            '<input type="text" name="spec_pack_num[]" class="input-text goods-edit-input" />'+
            '</div>'+
            '</div>'+
            '<div class="col-md-10 mt-10">'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">* 重量：</span>'+
            '<input type="text" name="spec_weight[]" class="input-text goods-edit-input" /> 克'+
            '</div>'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">* 净含量：</span>'+
            '<input type="text" name="spec_weight_net[]" class="input-text goods-edit-input" /> 克'+
            '</div>'+
            '</div>'+
            '<div class="col-md-10 mt-10">'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">长：</span>'+
            '<input type="text" name="spec_long[]" class="input-text goods-edit-input" /> M'+
            '</div>'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">宽：</span>'+
            '<input type="text" name="spec_wide[]" class="input-text goods-edit-input" /> M'+
            '</div>'+
            '</div>'+
            '<div class="col-md-10 mt-10">'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">高：</span>'+
            '<input type="text" name="spec_height[]" class="input-text goods-edit-input" /> M'+
            '</div>'+
            '<div class="col-md-5 spec_price_buying-div">'+
            '<span class="text-r edit-label">* 供应价：</span>'+
            '<input type="text" name="spec_price_buying[]" class="input-text goods-edit-input spec_price_buying" /> 元'+
            '</div>'+
            '</div>'+
            '<div class="col-md-10 mt-10">'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">* 库存：</span>'+
            '<input type="text" name="spec_num[]" class="input-text goods-edit-input" /> 件'+
            '</div>'+
            '<div class="col-md-5 spec_price-div">'+
            '<span class="text-r edit-label">* 零售价：</span>'+
            '<input type="text" name="spec_price[]" class="input-text goods-edit-input spec_price" /> 元'+
            '</div>'+
            '</div>'+
            '<div class="col-md-10 mt-10">'+
            '<div class="col-md-5 spec_travel">'+
            '<span class="text-r edit-label">旅行社分成：</span>'+
            '<input type="text" name="spec_travel_agency_rate[]" class="input-text goods-edit-input spec_travel_agency_rate"/> 按百分比'+
            '</div>'+
            '<div class="col-md-5 spec_guide">'+
            '<span class="text-r edit-label">导游分成：</span>'+
            '<input type="text" name="spec_guide_rate[]" class="input-text goods-edit-input spec_guide_rate"/> 按百分比'+
            '</div>'+
            '</div>' +
            '<div class="col-md-10 mt-10">'+
            '<div class="col-md-5">'+
            '<span class="text-r edit-label">平台服务费：</span>'+
             '<input type="hidden" name="spec_platform_fee[]" class="spec_platform_fee_input"/>'+
            '<input type="text"  class="input-text goods-edit-input spec_platform_fee" disabled /> 元'+
            '</div>'+
                    '<div class="col-md-5 spec_price-div">'+
                    '<span class="text-r edit-label">* 原价：</span>'+
                    '<input type="text" name="spec_price_market[]" class="input-text goods-edit-input spec_price" /> 元'+
                    '</div>'+
            '</div>'
            $('.spec-add').before(html);
            spac_platform_fee();
        }
        $(".auditPass").click(function(){
            var serverCharge = $(".servercharge").val();
            if(serverCharge < 0 ){
                layer.msg('平台服务费不能小于零');
                return false;
            }
        });

    </script>
@stop
