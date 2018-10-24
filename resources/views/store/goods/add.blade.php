@extends('supplier')
<style>
    .uploadify-queue{display: none;}
    .tab-del{display: block;width: 19px;height: 19px;position: relative;left: 950px;background: url("{{asset('images/icon_del.png')}}");cursor: pointer; }
    .img-del{float: left;background: url("{{asset('images/acrossTab-close.png')}}") -91px -13px;cursor: pointer;width: 13px;height: 13px;display: inline-block;position: relative;left: -12px;}

    .radio{cursor: pointer;display: inline-block;position: absolute;left: -5px;top:100px;}

    .goodsTable{width:800px !important;}
    .limitText{width: 250px;}
    .error-msg{
        background-color: #ef8282;
        display: block;
        text-align: center;
        padding: 5px;
        color: #fff;
    }

</style>
@section('content')
        <div class="rightCon">
            <div class="wrap">
                <form class="form" action="{{url('/goods')}}" method="post" id="goods-create">
                    <div class="box">
                        <h4>发布商品</h4>
                        <img  id="testimg" src="" style="display:none">
                        <table>
                            <tr>
                                <th width="114"><b class="noempty">*</b>商品品类：</th>
                                <td width="400">
                                    <select name="category_id">
                                        @foreach($conf_categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </td>

                            </tr>
                            <tr>
                                <th width="114"><b class="noempty">*</b>商品名称：</th>
                                <td width="500">
                                    <input type="text" name="title" datatype="*1-40" nullmsg="请输入商品名称"  width="40" style="width:520px;" maxlength="40"/>
                                </td>
                                <td><span class="Validform_checktip"></span></td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>所属分馆：</th>
                                <td>
                                    <select name="pavilion">
                                        @forelse($pavilions as $pavilion)
                                            <option value="{{$pavilion->id}}">{{$pavilion->name}}</option>
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
                                        <span class="cover"></span>
                                        <input type="hidden" name="cover" />
                                        <span>
                                            <label class="fileUpload">
                                                <input type="file" id="cover-upload" accept="image/jpeg,image/png,image/gif"/>
                                            </label>
                                        </span>
                                    </div>
                                    <p class="tip">（建议尺寸：700*320像素，封面图将用于商品列表等~）</p></td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <th><b class="noempty">*</b>轮播图：</th>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="fileResult oneFileResult fileResultbox">
                                        <span></span>
                                        <span class="upload-btn">
                                            <label class="fileUpload">
                                                <input type="file" id="images-upload" multiple="multiple" accept="image/jpeg,image/png,image/gif"/>
                                            </label>
                                        </span>
                                    </div>
                                    <p class="tip">（建议尺寸：800*800像素，请添加5~9张图片）</p>
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <th>重要提示：</th>
                            </tr>
                            <tr>

                                <td colspan="2" class="textCount">

                                    <textarea maxlength="60" name="important_tips"></textarea>
                                    <span class="textCountNum"><i>0</i>/60</span>

                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                    <div class="box">
                        <h4>商品信息</h4>
                        <table class="rightTh">
                            <tr>
                                <th><b class="noempty">*</b>规格名称：</th>
                                <td width="240">
                                    <input type="text" name="spec_name[]" datatype="*1-40" nullmsg="请输入规格名称"  width="40" style="width:220px;"/>
                                </td>
                                <td width="180"></td>
                                <th>包装件数：</th>
                                <td><input type="text" style="width:220px" name="pack_num[]"></td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>重量：</th>
                                <td>
                                    <input type="text" name="weight[]" datatype="s1-40" nullmsg="请输入数量"  width="40" style="width:180px;"/> 克
                                <td></td>
                                <th><b class="noempty">*</b>净含量：</th>
                                <td>
                                    <input type="text" name="weight_net[]" datatype="s1-40" nullmsg="请输入净含量"  width="40" style="width:180px;"/> 克
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th>长度：</th>
                                <td><input type="text" name="long[]" />M</td>
                                <td></td>
                                <th>宽度：</th>
                                <td><input type="text" name="wide[]" />M</td>
                                <td></td>
                            </tr>
                            <tr>
                                <th>高度：</th>
                                <td><input type="text" name="height[]" />M</td>
                                <td></td>
                                <th><b class="noempty">*</b>供应价：</th>
                                <td><input type="text" name="price_buying[]" datatype="s1-10" nullmsg="请输入供应价" maxlength="10" style="width:180px;"/>元</td>
                                <td></td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>库存：</th>
                                <td>
                                    <input type="text" name="num[]" datatype="s1-40" nullmsg="请输入库存"  width="40" style="width:65px;"/>
                                    件<span class="stock">已售出：<i>0</i>件</span></td>
                                <td></td>
                                <th><b class="noempty">*</b>建议零售价：</th>
                                <td><input type="text" name="price[]" datatype="s1-10" nullmsg="请输入建议零售价"  maxlength="10"  style="width:180px;"/>元</td>
                                <td></td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>原价：</th>
                                <td><input type="text" name="price_market[]" datatype="s1-10" nullmsg="请输入原价"  maxlength="10"  style="width:180px;"/>元</td>
                            </tr>
                        </table>
                        <div class="addButton"><input type="button" value="添加规格" class="addTable"></div>
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
                                            <input type="checkbox" checked disabled class="express_fee_mode">满{{$param['total_amount']}}元包邮，未满另加{{$param['express_amount']}}元
                                        </td>
                                    @endif
                                    @if($param['is_pick_up'] == 1)
                                        <td>
                                            <input type="checkbox" checked disabled class="is_pick_up">自提</label>
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
                                <td width="200">
                                    <input type="text" name="send_out_address" datatype="s1-16" nullmsg="请输入发货地" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"/>
                                </td>
                                <td width="180"></td>
                                <th><b class="noempty">*</b>产地：</th>
                                <td width="200">
                                    <input type="text" name="product_area" datatype="s1-16" nullmsg="请输入产地" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"/>
                                </td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>保质期：</th>
                                <td><input type="text" name="shelf_life" datatype="s1-16" nullmsg="请输入保质期" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"/></td>
                                <td width="180"></td>
                                <th><b class="noempty">*</b>贮藏：</th>
                                <td><input type="text" name="store" datatype="s1-16" nullmsg="请输入贮藏" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"/></td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>包装：</th>
                                <td><input type="text" name="pack" datatype="s1-16" nullmsg="请输入包装" style="width: 185px;" placeholder="最多输入16个字符" maxlength="16"/></td>
                                <td width="180"></td>
                                <th><b class="noempty">*</b>快递说明：</th>
                                <td><input type="text" name="express_desc" datatype="*1-40" nullmsg="请输入快递说明" style="width: 185px;" placeholder="最多输入40个字符" maxlength="40"/></td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>发货说明：</th>
                                <td><input type="text" name="send_out_desc" datatype="*1-40" nullmsg="请输入发货说明" style="width: 185px;" placeholder="最多输入40个字符" maxlength="40"/></td>
                                <td width="180"></td>
                                <th><b class="noempty">*</b>售后说明：</th>
                                <td><input type="text" name="sold_desc" datatype="*1-120" nullmsg="请输入售后说明" style="width: 185px;" placeholder="最多输入120个字符" maxlength="120"/></td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th>食品添加剂：</th>
                                <td><input type="text" name="food_addiitive" placeholder="最多输入16个字符" maxlength="16"/></td>
                                <td width="180"></td>
                                <th>生产许可证：</th>
                                <td><input type="text" name="product_license" placeholder="最多输入16个字符" maxlength="16"/></td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th>等级：</th>
                                <td><input type="text" name="level" placeholder="最多输入16个字符" maxlength="16"/></td>
                                <td width="180"></td>
                                <th>制造厂商/公司：</th>
                                <td><input type="text" name="company" placeholder="最多输入16个字符" maxlength="16"/></td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th rowspan="3">配料表：</th>
                                <td rowspan="3"><textarea style="width:200px; height:130px;" name="food_burden" placeholder="最多输入120个字符" maxlength="120"></textarea></td>
                                <td width="180" rowspan="3"></td>
                                <th>经销商：</th>
                                <td><input type="text" name="dealer" placeholder="最多输入16个字符" maxlength="16"/></td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th>地址：</th>
                                <td><input type="text" name="address" placeholder="最多输入20个字符" maxlength="20"/></td>
                                <td width="180"></td>
                            </tr>
                            <tr>
                                <th>特别说明：</th>
                                <td><input type="text"  name="remark" placeholder="最多输入120个字符" maxlength="120"/></td>
                                <td width="180"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="box">
                        <h4>赠品信息</h4>

                        <div class="addButton"><input type="button" value="添加赠品" class="popupShow"></div>
                    </div>
                    <div class="box">
                        <h4>商品详情</h4>
                        <script type="text/plain" id="myEditor" name="myEditor" style="width:100%;height:240px;"></script>
                    </div>
                    <div class="footButton">
                        <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                        <input type="submit" class="submit" value="发布商品">
                    </div>
                </form>
            </div>
        </div>

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
                                @forelse($goods as $val)
                                    <option value="{{$val->id}}">{{$val->title}}</option>
                                @empty
                                @endforelse
                        </select>

                            <input type="hidden" name="giftCover" value="@if(isset($goods->first()->cover)){{$goods->first()->cover}}@endif"/>

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
        /*var i = $("#a").val();
        if(i){
            layer.confirm('您没有设置运费<br>请前往在设置', {
                time: 20000, //20s后自动关闭
                yes: function(index, layero){
                    location.href = '/supplierExpress';
                }
            });
        }*/

        $(function(){
            $("#goods-create").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'yes') {
                        layer.alert(data.msg, {icon:1,time:1000});
                        location.href = '{{url('goods/review')}}';
                        //popupConfirm('applySuccess');
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
                'multi':false,
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
                'multi':true,
                'onUploadSuccess': function (file, data, response) {
                    if(data){
                        $('#testimg').attr('src','{{env('IMAGE_DISPLAY_DOMAIN')}}/'+$.parseJSON(data).img);                                                        $('#testimg').one('load',function() {
                            var imgWidth = this.width;
                            var imgHeight = this.height;
                            if (imgWidth !== imgHeight ) {
                                alert('请上传1:1尺寸图片')
                            } else {
                                data = JSON.parse(data);
                                if($('.upload-img').length >= 9){
                                    alert('最多上传9张轮播图');
                                }else{
                                    var html = '<span class="images" style="position: relative;padding-bottom:30px;background:none;">' +
                                            '<div class="radio"><input type="radio" name="img" value="{{env('IMAGE_DISPLAY_DOMAIN')}}/'+data.img+'"/>设置为封面</div>'+
                                            '<img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/' + data.img + '" alt="" width="100" height="100" class="upload-img"></span>' +
                                            '<i class="img-del"></i>' +
                                            '<input type="hidden" name="images[]" value="' + data.img + '"></div>';
                                    $('.upload-btn').before(html);
                                    if($('.upload-img').length = 1){
                                        $('input[type="radio"]').eq(0).attr("checked","checked");
                                    }
                                    if($('.upload-img').length >= 9){
                                        $('.upload-btn').hide();
                                    }
                                }
                                $('.radio input').click(function(){
                                    $(this).parents(".images").next("i").next("input").prependTo(".fileResultbox");
                                    $(this).parents(".images").next("i").prependTo(".fileResultbox");
                                    $(this).parents(".images").prependTo(".fileResultbox");
                                })
                                //轮播图删除
                                images_remove();
                            }
                        })
                    }
                }
            });
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
            $(".popupWrap .buttonGroup .submit").click(function () {
                $(".popupBg,.popupWrap").hide();
                var giftId = $('[name=gift]').val();
                var giftText = $('[name=gift] option:selected').text();
                var giftSpec = $('[name=spec]').val();
                var giftSpecText = $('[name=spec] option:selected').text();
                if(giftId > 0 && giftSpec > 0){
                    var giftHtml =
                        '<table class="goodsTable presentTable gift-' + giftId + '-'+giftSpec+'">' +
                        '<tr>'+
                        '<td width="50">'+giftId+'</td>'+
                        '<td width="350"><dl>'+
                        '<dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/'+$('[name=giftCover]').val()+'"></dt>'+
                        '<dd><p class="limitText">'+giftText+'</p><p>供货价：'+$('.price').text()+'</p></dd>'+
                        '</dl></td>'+
                        '<td width="100">规格：'+giftSpecText+'</td>'+
                        '<td width="100">库存'+$('.stock span').text()+'件</td>'+
                        '<td width="100">' +
                        '<input type="hidden" name="giftId[]" value="'+giftId+'">' +
                        '<input type="hidden" name="giftSpec[]" value="'+giftSpec+'">' +
                        '<a href="javascript:void(0)" class="deleteThis">删除</a></td>'+
                        '</tr>'+
                        '</table>';
                    $('.gift-' + giftId + '-' + giftSpec).remove();
                    $('.popupShow').parent().before(giftHtml);
                }

                gift_remove();
            })
        });

        //轮播图删除
        function images_remove() {
            $('.img-del').bind('click', function () {
                $(this).prev().remove();
                $(this).next().remove();
                $(this).remove();
                $('.upload-btn').show();
            })
        }
        //规格删除
        function tab_remove() {
            $('.tab-del').bind('click', function () {
                $(this).next().remove();
                $(this).remove();
            })
        }
        //赠品删除
        function gift_remove(){
            $('.deleteThis').bind('click',function(){
                $(this).parents('tr').remove();
            });
        }
    </script>
@stop
