@extends('wx.layout')
@section('title')
    申请售后
@endsection
@section('content')
    <div class="content myOrder">
        <link rel="stylesheet" type="text/css" href="{{ asset('lib/jQuery.filer/css/jquery.filer.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('lib/jQuery.filer/css/themes/jquery.filer-dragdropbox-theme.css') }}">
        @foreach($orderdetails as $orderdetail)
            <div class="mb-6">
                <h3>{{$orderdetail->supplierinfo[0]['store_name']}}</h3>

                <div class="orderGoods lineB">
                    @foreach($orderdetail->data as $goods)
                        <dl class="info lineB">
                            <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods['goodsbase']->cover}}?imageslim"></dt>
                            <dd>
                                <p class="name">{{$goods['goodsbase']->title}}</p>
                                <p>规格：{{$goods['goodsinfo']->spec_name}}<span class="amount">数量x{{$goods['goodsinfo']->num}}</span></p>
                                <p>价格：<span class="price">￥{{$goods['goodsinfo']->price}}</span></p>
                            </dd>
                        </dl>
                    @endforeach
                    @if(isset($orderdetail->gift))
                        @foreach($orderdetail->gift as $gift)
                            <dl class="info lineB">
                                <dt class="lineT br4">
                                    <span class="gifts">活动赠品</span>
                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$gift['cover_image']}}?imageslim">
                                </dt>
                                <dd>
                                    <p class="name">{{$gift['title']}}</p>
                                    <p>规格：{{$gift['spec_name']}}<span class="amount">数量{{$gift['num']}}</span></p>
                                    <p>价格：<span class="price">￥{{$gift['price']}}</span></p>
                                </dd>
                            </dl>
                        @endforeach
                    @endif
                    <div class="total">共{{$orderdetail->goodsnum}}件商品<span>合计：<b class="price">￥{{$orderdetail->sumprice}}</b><b class="freight">({{$orderdetail->pay_way}})</b></span></div>
                </div>

                <form method="post" action="/aftersales/{{$orderdetail->order_no}}" id="formDate">

                    <div class="reason lineB mb-6">
                        <dl>
                            <dt>问题描述：</dt>
                            <dd><div class="textCount"><textarea class="z-content" name="content" maxlength="120"></textarea><span class="textCountNum"><i>0</i>/120</span></div></dd>
                        </dl>
                    </div>
                    <div class="reason lineB mb-6">
                        <dl>
                            <dt>添加图片：</dt>
                            <dd>
                                <div class="fileResult" style="min-height:70px;">
                        <span class="fileUpload">
                            <label><input type="file" name="files[]" id="image_upload" accept="image/jpg,image/jpeg,image/png,image/gif" multiple/></label>
                        </span>
                                </div>
                            </dd>
                        </dl>
                    </div>
                    <p class="importTip">为了帮助我们更好的解决问题，请填写问题描述和上传图片；问题描述不超过120个字符，图片最多不超过５张。</p>
            </div>
    </div>
    <div class="footButton">
        <input type="button" class="button" value="提交" onclick="postmessage({{$orderdetail->order_no}})">
    </div>
    </form>
    @endforeach
@endsection
@section('javascript')
    <script type="text/javascript" src="/wx/js/pict.js"></script>

    <script>
        function postmessage(order_no){
        	$(".footButton input").attr("disabled","disabled");
            var content = $(".z-content").val();
            var image = $('.image');
            if(image.length < 1 && content.length < 1){
                information('请添加图片或问题');
                return false;
            }
            $.post('/afterOrderState',{order_no:order_no},function(data){
                console.log(data);
                if(data.ret == 'yes'){
                    $("#formDate").submit();
                };
                if(data.ret == 'no'){
                    information(data.msg);
                    $(".footButton input").removeAttr("disabled");
                }
            })


        }

        $(function(){
            var _upFile=document.getElementById("image_upload");
            _upFile.addEventListener("change",function(){

                var result = $(this).parents(".fileResult");
                var spanLen = $(this).parents(".fileResult").children("span").length;
                if (_upFile.files.length === 0) {
                    alert("请选择图片");
                    return; }
                var length = _upFile.files.length;
                if(length + spanLen > 6){
                    information("最多只能上传五张");
                    return false;
                }

                for(i=0;i<length;i++){
                    var oFile = _upFile.files[i];
                    if(!new RegExp("(jpg|jpeg|png|gif)+","gi").test(oFile.type)){
                        alert("照片上传：文件类型必须是JPG、JPEG、PNG");
                        return false;
                    }

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var base64Img= e.target.result;
                        var _ir=ImageResizer({
                            resizeMode:"auto"
                            ,dataSource:base64Img
                            ,dataSourceType:"base64"
                            ,maxWidth:1200 //允许的最大宽度
                            ,maxHeight:600 //允许的最大高度。
                            ,onTmpImgGenerate:function(img){

                            }
                            ,success:function(resizeImgBase64,canvas){
                                $.post('/mine/upload',{imgOne:resizeImgBase64},function(data){
                                    $(".fileResult").prepend('<span class="img">' +
                                                    '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}' + data +'" width = "66px" height = "66px">' +
                                                    '<input class="image" type="hidden" name="image[]" value="' + data +'">' +
                                                    '<i class="delete"></i>' +
                                                    '</span>');
                                    $(".fileResult .img").click(function(){
                                        $(this).remove();
                                        $(".fileUpload").show();
                                    });
                                })
                            }
                            ,debug:false
                        });
                    };
                    reader.readAsDataURL(oFile);
                }
                if(length + spanLen == 6){
                    $(".fileUpload").hide();
                }
            },false);
        });

    </script>
@endsection