@extends('wx.layout')
@section('title')
    发表评价
@endsection
@section('content')
<style>
	.myOrder .total{padding:14px  0;}
	.myOrder .fileUploads label{background: url(/wx/images/addIcon.png)no-repeat;background-size: 62px 62px;}
	.linkArrow .arrowicon {
	    display: inline-block;
	    line-height: 44px;
	    height: 44px;
	    color: #999;
	    padding-right: 10px;
	    background: url(/wx/images/right_arrow.png) no-repeat center;
	    background-size: 6px 12px;
	    position: absolute;
	    top: 0px;
	    margin-left: 10px;
	}
</style>
	<div class="headerBg">
		<div class="back" onclick="javascript:history.go(-1)"></div>
		<div class="title">发表评价</div>
	</div>
    <div class="content myOrder">
        <link rel="stylesheet" type="text/css" href="{{ asset('lib/jQuery.filer/css/jquery.filer.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('lib/jQuery.filer/css/themes/jquery.filer-dragdropbox-theme.css') }}">
            <div class="mb-6">
                <ul class="goodsSpec lineB">
		            <li class="lineB">
		                <a href="/supplier/1" class="linkArrow">
		                    <span class="yyg-color9">{{$store_name}}</span>
		                </a>
		            </li>    
		        </ul>
		        <!-- 评价表单 -->
                <form method="post" action="{{url('/mine/savecomment')}}" id="formDate">
		        @if(!empty($ordergoods))
		        @foreach($ordergoods as $key=>$good)
                <div class="orderGoods lineB">
                    <dl class="info lineB">
                        <dt>
                        @if(!empty($good->coverimg))
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$good->coverimg}}?imageslim">
                        @endif
                        </dt>
                        <dd>
                            <p class="name">{{$good->goods_title}}</p>
                            <p>规格：{{$good->spec_name}}<span class="amount">数量x{{$good->num}}</span></p>
                            <p><span class="price">￥{{$good->price}}</span></p>
                        </dd>
                    </dl>
                </div>

                    <div class="reason lineB">
                        <dl>
                            <dt>评价内容：</dt>
                            <dd>
                                <div class="textCount">
                                    <input type="hidden" name="comment{{$key}}[uid]" value="{{$good->uid}}">
                                    <input type="hidden" name="comment{{$key}}[goods_id]" value="{{$good->goods_id}}">
                                    <input type="hidden" name="comment{{$key}}[order_no]" value="{{$good->order_no}}">
                                    <input type="hidden" name="comment{{$key}}[spec_id]" value="{{$good->spec_id}}">
                                    <textarea class="z-content" name="content[]" maxlength="120"></textarea>
                                    
                                    <span class="textCountNum"><i>0</i>/120</span>
                                </div>
                            </dd>
                        </dl>
                    </div>
                    <div class="reason lineB mb-6">
                        <dl>
                            <dd>
                                <div class="fileResult{{$key}} fileResult" style="min-height:70px;">
			                        <span class="fileUpload{{$key}} fileUploads fileUpload">
			                            <label><!-- id="image_upload" -->
			                            	<input type="file" id="image_upload{{$key}}" onchange="hero({{$key}})"  accept="image/jpg,image/jpeg,image/png,image/gif" multiple/>
			                            </label>
			                        </span>
                                </div>
                            </dd>
                            <dd class="yyg-color9">建议上传图片大小1M.内,最多上传9张</dd>
                        </dl>
                    </div>
                    <div class="footButton">
                    	<input type="button" onclick="postmessage()" class="button" value="提交" >
                    </div>
                @endforeach
                @endif
                </form>
                <!-- 评价表单 -->
            </div>
    </div>
    
@endsection
@section('javascript')
    <script type="text/javascript" src="/wx/js/pict.js"></script>
    <script>
        function postmessage(){
            var istrue = false;//onclick="postmessage()"
            $(".z-content").each(function(e){
            	var content = $(".z-content").eq(e).val();
                if(content.length < 1){
                    information('请填写评价内容');
                    return istrue = false;
                }
                istrue = true;
            })
            if(istrue==true){
          		$("#formDate").submit();
            }
        }
        function hero(num){
        	var imglength = 9;
            var _upFile=document.getElementById("image_upload"+num);
            var result = $(this).parents(".fileResult"+num);
            var spanLen = $(".fileResult"+num).find(".img").length;
            if (_upFile.files.length === 0) {
                information("请选择图片");
                return; }
            var length = _upFile.files.length;
            if(length + spanLen > imglength){
                information("最多只能上传9张");
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
                                $(".fileResult"+num).prepend('<span class="img">' +
                                                '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}' + data +'" width = "66px" height = "66px">' +
                                                '<input class="image" type="hidden" name="image'+num+'[]" value="' + data +'">' +
                                                '<i class="delete"></i>' +
                                                '</span>');
                                $(".fileResult"+num+" .img").click(function(){
                                    $(this).remove();
                                    $(".fileUpload"+num).show();
                                });
                            })
                        }
                        ,debug:false
                    });
                };
                reader.readAsDataURL(oFile);
            }
            if(length + spanLen == imglength){
                $(".fileUpload"+num).hide();
            }
        }
        
        
        
    </script>
@endsection