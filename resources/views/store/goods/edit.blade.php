@extends('supplier')
<style>
    .uploadify-queue{display: none;}
    .tab-del{display: block;width: 19px;height: 19px;position: relative;left:950px;background: url("{{asset('images/icon_del.png')}}");cursor: pointer; }
    .img-del{
    	float: left;background: url("{{asset('images/acrossTab-close.png')}}") -91px -13px;
    	cursor: pointer;width: 13px;height: 13px;
    	display: inline-block;position: relative;left: 100px;
    	}
    .radio{cursor: pointer;display: inline-block;position: absolute;left: 8px;top:100px;}
    .images-div {
            display: inline-block;
            position: relative;padding-bottom:30px;background:none;
        }
    .goodsTable{width:800px !important;}
    .limitText{width: 250px;}
    .price-disabled{margin-left: 20px;}
</style>
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <form class="form" action="/goods/{{$goods->id}}/edit" method="post" id="goods-edit">
                <div class="box">
                    <h4>发布商品</h4>
                    <table>
                        <tr>
                            <th width="114"><b class="noempty">*</b>商品品类：</th>
                            <td width="400" colspan="3">
                                <select name="category_id">
                                    @forelse($goods->conf_categories as $category)
                                        <option value="{{$category->id}}" @if($goods->category_id == $category->id) selected @endif>{{$category->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th width="114"><b class="noempty">*</b>商品名称：</th>
                            <td width="500"><input type="text" name="title" value="{{isset($goods->title)?$goods->title:''}}" datatype="*1-40" nullmsg="请输入商品名称"  width="40" style="width:520px;" maxlength="40"/></td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>所属分馆：</th>
                            <td>
                                <select name="pavilion">
                                    @forelse($goods->pavilions as $pavilion)
                                        <option value="{{$pavilion->id}}" @if($goods->pavilion == $pavilion->id) selected @endif>{{$pavilion->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>橱窗图：</th>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="fileResult oneFileResult">
                                    <span class="cover"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{isset($goods->cover)?$goods->cover:'img-null.jpg'}}" alt="" width="220" height="100"></span>
                                    <input type="hidden" name="cover" value="{{isset($goods->cover)?$goods->cover:''}}"/>
                                    <span>
                                            <label class="fileUpload">
                                                <input type="file" id="cover-upload" accept="image/jpeg,image/png,image/gif"/>
                                            </label>
                                        </span>
                                </div>
                                <p class="tip">（建议尺寸：700*320像素，封面图将用于商品列表等~）</p>
                            </td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>轮播图：</th>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="fileResult oneFileResult">
                                	<span class="fileResultbox" style="background: none;">
                                    @forelse($goods->images as $key => $image)
                                    	<div class="images-div photos" data-id="{{$image->id}}">
	                                        <div class="radio"><input type="radio" name="img" value="{{env("IMAGE_DISPLAY_DOMAIN")}}/{{$image->name}}" @if($key == 0)checked="checked"@endif>设置为封面</div>
	                                        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$image->name}}" alt="" width="100" height="100" class="upload-img">
	                                        <i class="img-del" data-id="{{$image->id}}"></i>
	                                        <input type="hidden" name="image_add[]" value="{{$image->name}}">
                                    	</div>
                                    	@empty
                                    @endforelse
                                    </span>
                                    @if(!empty($goods->images))
                                    <span class="upload-btn"@if($goods->images->count() >= 9)style="display: none;" @endif>
                                        <label class="fileUpload">
                                            <input type="file" id="images-upload" multiple="multiple" accept="image/jpeg,image/png,image/gif"/>
                                        </label>
                                    </span>
                                    @else
                                        <span class="upload-btn">
                                            <label class="fileUpload">
                                                <input type="file" id="images-upload" multiple="multiple" accept="image/jpeg,image/png,image/gif"/>
                                            </label>
                                        </span>
                                    @endif
                                </div>
                                <p class="tip">（建议尺寸：800*800像素，请添加5~9张图片）</p>
                            </td>
                        </tr>
                        <tr>
                            <th>重要提示：</th>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="textCount"><textarea maxlength="60" name="important_tips" >{{isset($goods->ext->important_tips)?$goods->ext->important_tips:''}}</textarea><span class="textCountNum"><i>0</i>/60</span></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <h4>商品信息</h4>
                    @forelse($goods->spec as $key=>$spec)
                        @if($goods->state == 3)
                            @if($key > 0)<span class="tab-del" data-id="{{$spec->id}}"></span>@endif
                        @endif
                    <table class="rightTh">
                        <tr>
                            <th><b class="noempty">*</b>规格名称：</th>
                            <td width="240"><input type="text" name="spec_name[]" value="{{isset($spec->name)?$spec->name:''}} " datatype="*1-40" nullmsg="请输入规格名称"  width="40" style="width:180px;"/></td>
                            <td width="180"></td>
                            <th>包装件数：</th>
                            <td><input type="text" name="pack_num[]" value="{{isset($spec->pack_num)?$spec->pack_num:''}}" /></td>
                            <td width="180"></td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>重量：</th>
                            <td><input type="text"  name="weight[]"  value="{{isset($spec->weight)?$spec->weight:''}}" datatype="s1-40" nullmsg="请输入重量"  width="40" style="width:180px;"/>克</td>
                            <td></td>
                            <th><b class="noempty">*</b>净含量：</th>
                            <td><input type="text" name="weight_net[]"  value="{{isset($spec->weight_net)?$spec->weight_net:''}}" datatype="s1-40" nullmsg="请输入净含量"  width="40" style="width:180px;"/>克</td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>长度：</th>
                            <td><input type="text" name="long[]" value="{{isset($spec->long)?$spec->long:''}}" />M</td>
                            <td></td>
                            <th>宽度：</th>
                            <td><input type="text" name="wide[]" value="{{isset($spec->wide)?$spec->wide:''}}" />M</td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>高度：</th>
                            <td><input type="text" name="height[]" value="{{isset($spec->height)?$spec->height:''}}" />M</td>
                            <td></td>
                            <th><b class="noempty">*</b>供应价：</th>
                            <td><input type="text" name="price_buying[]" value="{{isset($spec->price_buying)?$spec->price_buying:''}}" datatype="s1-40"
                                       nullmsg="请输入供应价"  maxlength="10" style="width:180px;" @if($goods->state == 2) disabled @endif/>元 @if($goods->state == 2) <span class="price-disabled">不可修改</span> @endif</td>
                            <td></td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>库存：</th>
                            <td><input type="text" name="num[]" value="{{isset($spec->num)?$spec->num:''}}"datatype="s1-40" nullmsg="请输入库存" maxlength="7" style="width:70px;" />
                                件<span class="stock">已售出：<i>{{$goods['num_sold']}}</i>件</span></td>
                            <td></td>
                            <th><b class="noempty">*</b>建议零售价：</th>
                            <td><input type="text" name="price[]" value="{{isset($spec->price)?$spec->price:''}}" datatype="s1-10" nullmsg="建议零售价" maxlength="10" style="width:180px;" @if($goods->state == 2) disabled @endif/>元  @if($goods->state == 2) <span class="price-disabled">不可修改</span> @endif</td>
                            <td></td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>原价：</th>
                            <td><input type="text" name="price_market[]" value="{{isset($spec->price_market)?$spec->price_market:''}}" datatype="s1-10" nullmsg="请输入原价"  maxlength="10"  style="width:180px;"/>元</td>
                        </tr>
                        {{--<tr>
                        <th><b class="noempty">*</b>运费：</th>
                        <td><label class="checkbox">
                                <input type="hidden" name="express_fee_mode[]" value="{{isset($spec->express_fee_mode)?$spec->express_fee_mode:0}}">
                                <input type="checkbox"  class="express_fee_mode" @if(1 == $express_fee_mode = isset($spec->express_fee_mode)?$spec->express_fee_mode:'') checked @endif>全国包邮</label></td>
                        <td></td>
                        <th></th>
                        <td><label class="checkbox">
                                <input type="hidden" name="is_pick_up[]" value="{{isset($spec->is_pick_up)?$spec->is_pick_up:0}}">
                                <input type="checkbox" class="is_pick_up" @if(1 == $is_pick_up = isset($spec->is_pick_up)?$spec->is_pick_up:0) checked @endif>是否允许自提</label></td>
                        <td></td>
                        </tr>--}}
                    </table>
                    <input type="hidden" name="spec_id[]" value="{{isset($spec->id)?$spec->id:''}}" />
                        @empty
                    @endforelse
                    @if($goods->state == 3)
                        <div class="addButton"><input type="button" value="添加规格" class="addTable"></div>
                    @endif
                </div>
                @if(!empty($param))
                @if($param['state'] == 0 && $param['is_pick_up'] == 0)
                @else
                    <div class="box">
                        <h4>邮费设置</h4>
                        <table class="rightTh">
                            <tr>
                                @if($param['state'] == 1)
                                    <td>
                                        <input type="checkbox" disabled checked  class="express_fee_mode">满{{$param['total_amount']}}元包邮，未满另加{{$param['express_amount']}}元
                                    </td>
                                @endif
                                @if($param['is_pick_up'] == 1)
                                    <td>
                                        <input type="checkbox" disabled checked class="is_pick_up">自提</label>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </div>
                @endif
                @endif
                <div class="box">
                    <h4>基本属性</h4>
                    <table class="rightTh">
                        <tr>
                            <th><b class="noempty">*</b>发货地：</th>
                            <td width="200"><input type="text" name="send_out_address" value="{{isset($goods->ext->send_out_address)?$goods->ext->send_out_address:''}}" datatype="s1-16" nullmsg="请输入发货地" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"/></td>
                            <td width="180"></td>
                            <th><b class="noempty">*</b>产地：</th>
                            <td width="200"><input type="text" name="product_area" value="{{isset($goods->ext->product_area)?$goods->ext->product_area:''}}" datatype="s1-16" nullmsg="请输入发货地" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"></td>
                            <td width="180"></td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>保质期：</th>
                            <td><input type="text" name="shelf_life" value="{{isset($goods->ext->store)?$goods->ext->store:''}}"  datatype="*" nullmsg="请输入发货地" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"/></td>
                            <td></td>
                            <th><b class="noempty">*</b>贮藏：</th>
                            <td><input type="text" name="store" value="{{isset($goods->ext->store)?$goods->ext->store:''}}" datatype="*" nullmsg="请输入贮藏" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>包装：</th>
                            <td><input type="text" name="pack" value="{{isset($goods->ext->pack)?$goods->ext->pack:''}}"  datatype="s1-16" nullmsg="请输入包装" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"/></td>
                            <td></td>
                            <th><b class="noempty">*</b>快递说明：</th>
                            <td><input type="text" name="express_desc" value="{{isset($goods->ext->express_desc)?$goods->ext->express_desc:''}}"  datatype="*1-40" nullmsg="请输入快递说明" style="width: 185px;" placeholder="最多输入40个字符" maxlength="40"/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>发货说明：</th>
                            <td><input type="text" name="send_out_desc" value="{{isset($goods->ext->send_out_desc)?$goods->ext->send_out_desc:''}}"  datatype="*1-40" nullmsg="请输入发货说明" style="width: 185px;" placeholder="最多输入40个字符" maxlength="40"/></td>
                            <td></td>
                            <th><b class="noempty">*</b>售后说明：</th>
                            <td><input type="text" name="sold_desc" value="{{isset($goods->ext->sold_desc)?$goods->ext->sold_desc:''}}"  datatype="*1-120" nullmsg="请输入售后说明" style="width: 185px;" placeholder="最多输入120个字符" maxlength="120"/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>食品添加剂：</th>
                            <td><input type="text" name="food_addiitive" value="{{isset($goods->ext->food_addiitive)?$goods->ext->food_addiitive:''}}"  placeholder="最多输入16个字符" maxlength="16"/></td>
                            <td></td>
                            <th>生产许可证：</th>
                            <td><input type="text" name="product_license" value="{{isset($goods->ext->product_license)?$goods->ext->product_license:''}}"  placeholder="最多输入16个字符" maxlength="16" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>等级：</th>
                            <td><input type="text" name="level" value="{{isset($goods->ext->level)?$goods->ext->level:''}}"  placeholder="最多输入16个字符" maxlength="16" /></td>
                            <td></td>
                            <th>制造厂商/公司：</th>
                            <td><input type="text" name="company" value="{{isset($goods->ext->company)?$goods->ext->company:''}}"  placeholder="最多输入16个字符" maxlength="16"/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th rowspan="3">配料表：</th>
                            <td rowspan="3">
                                <textarea style="width:200px; height:130px;" name="food_burden"  placeholder="最多输入120个字符" maxlength="120" >{{isset($goods->ext->food_burden)?$goods->ext->food_burden:''}}</textarea></td>
                            <td rowspan="3"></td>
                            <th>经销商：</th>
                            <td><input type="text" name="dealer" value="{{isset($goods->ext->dealer)?$goods->ext->dealer:''}}"   placeholder="最多输入16个字符" maxlength="16"/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>地址：</th>
                            <td><input type="text" name="address" value="{{isset($goods->ext->address)?$goods->ext->address:''}}"  placeholder="最多输入20个字符" maxlength="20"/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>特别说明：</th>
                            <td><input type="text"  name="remark" value="{{isset($goods->ext->remark)?$goods->ext->remark:''}}" placeholder="最多输入120个字符" maxlength="120"/></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <h4>赠品信息</h4>

                    @forelse($goods->gift as $gift)
                    <table class="goodsTable presentTable">
                        <tbody>
                        <tr>
                            <td width="50">{{$gift->goods->id}}</td>
                            <td width="350">
                                <dl>
                                    <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$gift->goods->cover}}"></dt>
                                    <dd><p class="limitText">{{$gift->goods->title}}</p>
                                        <p>供货价：￥{{$gift->spec->price_buying}}</p></dd>
                                </dl>
                            </td>
                            <td width="100">规格：{{$gift->spec->name}}</td>
                            <td width="100">库存{{$gift->spec->num}}件</td>
                            <td width="100">
                                <input type="hidden" value="{{$gift->goods->id}}" class="gift-goods-id">
                                <input type="hidden" value="{{$gift->spec->id}}" class="gift-spec-id">
                                <a href="javascript:void(0)" class="deleteThis" data-id="{{$gift->id}}">删除</a></td>
                        </tr>
                        </tbody>
                    </table>
                    @empty

                    @endforelse


                    <div class="addButton"><input type="button" value="添加赠品" class="popupShow"></div>
                </div>
                <div class="box">
                    <h4>商品详情</h4>
                    <script type="text/plain" id="myEditor" name="myEditor" style="width:100%;height:240px;">{!! $goods->ext->description !!}</script>
                </div>
                <div class="footButton">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                        @if($goods->state == 3)
                    <input type="hidden" name="review">
                    <input type="submit" class="button review" value="重新上传">
                        @endif
                    <input type="submit" class="save" value="保存">
                </div>
            </form>
        </div>
    </div>
    <input type="hidden" class="confirm" />
    <div class="popupBg"></div>
    <div class="popupWrap">
        <div class="title"><b>添加赠品</b></div>
        <div>
            <table>
                <tr>
                    <th>选择赠品商品：</th>
                    <td>
                        <select name="gift">
                        <option value="0">请选择商品</option>

                            @forelse($goods->gifts as $val)
                                <option value="{{$val->id}}">{{$val->title}}</option>
                            @empty
                            @endforelse

                        </select>
                        <input type="hidden" name="giftCover" value="{{isset($goods->gifts->first()->cover)?$goods->gifts->first()->cover:''}}"/>
                    </td>
                </tr>
                <tr>
                    <th>选择规格：</th>
                    <td>
                        <select name="spec">

                        <option value="0">请选择规格</option>

                        </select>
                    </td>
                </tr>
                <tr>
                    <th>库存：</th>
                    <td class="stock"><span>0</span>件</td>
                </tr>
                <tr>
                    <th>供货价：</th>
                    <td class="price">￥<span>0.00</span></td>
                </tr>
            </table>
            <div class="buttonGroup">
                <input type="button" class="cancel" value="取消">
                <input type="button" class="submit" value="确定">
            </div>
        </div>
    </div>
@stop
@section('footer')
    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('lib/umeditor/third-party/template.min.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('lib/umeditor/umeditor.config.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('lib/umeditor/umeditor.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('lib/umeditor/lang/zh-cn/zh-cn.js') }}"></script>
    <link href="{{ asset('lib/umeditor/themes/default/css/umeditor.css') }}" type="text/css" rel="stylesheet">
    <script>

        $(function(){
            $('.review').click(function(){
                $('[name=review]').val(1);
            });
            $('.save').click(function(){
                $('[name=review]').val(0);
            });
            var action = '{{$action}}';
            $("#goods-edit").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'yes') {
                        layer.open({
                            content: data.msg,
                            btn: ['确认'],
                            yes: function(index, layero) {
                                if (action == 'reviewEdit'){
                                    window.location.href= '{{url('goods/review/3')}}';
                                }else{
                                    window.location.href= '{{url('goods/lib/2')}}';
                                }

                            },
                            cancel: function() {
                                if (action == 'reviewEdit'){
                                    window.location.href= '{{url('goods/review/3')}}';
                                }else{
                                    window.location.href= '{{url('goods/lib/2')}}';
                                }
                            }
                        });
                    } else {
                        layer.alert(data.msg, {icon:2,time:5000});
                    }
                }
            });
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
        //实例化编辑器
        var um = UM.getEditor('myEditor');
        <?php $timestamp = time();?>
        $(function () {
            //封面图上传
            $('#cover-upload').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/goods/upload') }}',
                'buttonText': '',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 100,
                'width': 100,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    $('.cover').children().remove();
                    $('.cover').append('<img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/'+data.img+'" alt="" width="220" height="100">');
                    $('[name=cover]').val(data.img);
                }
            });
            //轮播图上传
            $('#images-upload').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/goods/upload') }}',
                'buttonText': '',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 100,
                'width': 100,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    if($('.upload-img').length >= 9){
                        alert('最多上传9张轮播图');
                    }else{
                    var images_div = '<div class="images-div photos">' +
                                '<div class="radio"><input type="radio" name="img" value="{{env('IMAGE_DISPLAY_DOMAIN')}}/'+data.img+'"/>设置为封面</div>'+
                                '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}/' + data.img + '" alt="" width="100" height="100"><i class="img-del img-dels"></i>' +
                                '<input type="hidden" name="image_add[]" value="' + data.img + '"></div>';
                        $('.fileResultbox').append(images_div);
                        if($('.upload-img').length = 1){
                            $('input[type="radio"]').eq(0).attr("checked","checked");
                        }
                    if($('.upload-img').length >= 9){
                        $('.upload-btn').hide();
                    }
                    //轮播图删除
                    $('.img-dels').click(function () {
                        if (confirm('是否删除图片？图片删除后将无法恢复！')) {
                            $(this).parent().remove();
                            $('.upload-btn').show();
                        }
                    });
                    //选择封面
                    selectCover ();
                }
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
            
            //选择赠品
            $('[name=gift]').bind('change',function(){
                $.get('/gift/specs/'+$(this).val(),function(json){
                    if(json.state == 1){
                        var html = '<option value="0">请选择规格</option>';
                        $.each(json.data,function(k,v){
                            html += '<option value="'+v.id+'">'+v.name+'</option>';
                        });
                        $('[name=spec]').children().remove();
                        $('[name=spec]').append(html);
                        $('.stock span').text(0);
                        $('.price span').text('0.00');
                        $('[name=giftCover]').val(json.goods.img);
                    }
                });
            });
            //选择规格
            $('[name=spec]').bind('change',function(){
                $.get('/gift/spec/'+$(this).val(),function(json){
                    if(json.state == 1){
                        var html = '';
                        $('.stock span').text(json.data.num);
                        $('.price span').text(json.data.price_buying);
                    }else{
                        $('.stock span').text(0);
                        $('.price span').text('0.00');
                    }
                });
            });
            //追加赠品
            $(".popupBg,.popupWrap .buttonGroup .submit").click(function () {
                $(".popupBg,.popupWrap").hide();
                var timestamp = Date.parse(new Date());
                var giftId = $('[name=gift]').val();
                var giftText = $('[name=gift] option:selected').text();
                var giftSpec = $('[name=spec]').val();
                var giftSpecText = $('[name=spec] option:selected').text();
                if(giftId > 0 && giftSpec > 0) {
                    var flag = 1;
                    $('.gift-goods-id').each(function(k,v){
                        if($(v).val() == giftId && $('.gift-spec-id').eq(k).val() == giftSpec){
                            flag = 0;
                        }
                    });
                    if(flag == 1){
                        var giftHtml =
                            '<table class="goodsTable presentTable gift-' + giftId + '-'+giftSpec+'">' +
                            '<tr>' +
                            '<td width="50">' + giftId + '</td>' +
                            '<td width="350"><dl>' +
                            '<dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/' + $('[name=giftCover]').val() + '"></dt>' +
                            '<dd><p class="limitText">' + giftText + '</p><p>供货价：' + $('.price').text() + '</p></dd>' +
                            '</dl></td>' +
                            '<td width="100">规格：' + giftSpecText + '</td>' +
                            '<td width="100">库存' + $('.stock span').text() + '件</td>' +
                            '<td width="100">' +
                            '<input type="hidden" name="giftId[]" value="' + giftId + '">' +
                            '<input type="hidden" name="giftSpec[]" value="' + giftSpec + '">' +
                            '<a href="javascript:void(0)" class="deleteThis" id="gift-' + timestamp + '">删除</a></td>' +
                            '</tr>' +
                            '</table>';
                        $('.gift-' + giftId + '-' + giftSpec).remove();
                        $('.popupShow').parent().before(giftHtml);
                    }

                }
                $('#gift-'+timestamp).click(function(){
                    $(this).parents('tr').remove();
                });
            });
            images_remove();
            tab_remove();
            gift_remove();
        });



        //轮播图删除
        function images_remove() {
            $('.img-del').click(function () {
                if(confirm('是否删除图片？图片删除后将无法恢复！')){
                    var image = $(this).attr('data-id');
                    if(image > 0){
                        $.get('/goods/ajaxEdit/' + image + '/image',function(data){

                        });
                    }
                    $(this).parent('.images-div').remove();
                    $('.upload-btn').show();
                }
            });
        }
        //规格删除
        function tab_remove() {
            $('.tab-del').bind('click', function () {
                if(confirm('是否删除规格？规格删除后将无法恢复！')){
                    var spec = $(this).attr('data-id');
                    if(spec > 0){
                        $.get('/goods/ajaxEdit/' + spec + '/spec',function(data){

                        });
                    }
                    $(this).next().remove();
                    $(this).remove();
                }

            })
        }
        //赠品删除
        function gift_remove(){
            $('.deleteThis').bind('click',function(){
                if(confirm('是否删除规格？规格删除后将无法恢复！')){
                    var gift = $(this).attr('data-id');
                    if(gift > 0){
                        $.get('/goods/ajaxEdit/' + gift + '/gift',function(data){

                        });
                    }
                    $(this).parents('tr').remove();
                }

            });
        }


    </script>
@stop
