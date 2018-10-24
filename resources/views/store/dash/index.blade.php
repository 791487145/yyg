@extends('supplier')
@section('content')
<link rel="stylesheet" href="{{asset('lib/pljs/jquery-ui.css')}}" type="text/css" />
<link rel="stylesheet" href="{{asset('lib/pljs/jquery.plupload.queue/css/jquery.plupload.queue.css')}}" type="text/css" />
    <style>
    .imgbut{
        margin-left: 10px;
    }
    #uploader_container{height: 0;display: none;}
    .statisNum{background: url({{url('/images/statisNum_bg.png')}}) no-repeat;}
    .setDiv{position: fixed;background: rgba(0,0,0,0.5);width: 100%;height: 100%;z-index: 2;}
    .form{position: absolute;left: 50%;top: 50%;transform: translate(-50%, -50%);background: #fff;}
    .form h4{font-size:18px;background: #E3E3E3;padding: 10px 20px;}
    .form .box{padding: 20px;width:620px;}
    .form select{width:100px;padding: 4px;}
    .Preservation{padding: 8px 20px;background:#e7641c;color: #FFF;display:block;margin: 20px auto;border: 0;cursor: pointer;}
    .inputText{width: 184px; height: 30px;border: 1px solid #6e6e6e;padding: 0 8px;margin-right: 14px;}
    .form .fileUpload {position: relative;display: inline-block;width: 100px;height: 100px;background: url(../images/file_icon_03.png);float: left;background-size: 100px;}
    .deletePopup{float: right;font-size: 16px;cursor: pointer;}
</style>
    
    @if($supplier->store_name=='')
	<div class="setDiv">
		<form class="form" method="post" action="{{url('supplier/AlertSetting')}}">
			<h4>请填写供应商信息 <span class="deletePopup">x</span></h4>
			<div class="box">
                <table>
                    <tr>
                        <th>真实姓名：</th>
                        <td><input class="inputText" type="text" name="name" readonly="readonly" value="{{$supplier->name}}"/></td>
                        <th>手机号：</th>
                        <td><input class="inputText" type="number" name="mobile" readonly="readonly" value="{{$supplier->mobile}}"/></td>
                    </tr>
                    <tr>
                    	<th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th>店铺名称：</th>
                        <td><input class="inputText" type="text" name="store_name" value="{{$supplier->store_name}}"/></td>
                    </tr>
                    <tr>
                    	<th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th>店铺logo：</th>
                        <td>
                        <!-- 图片放置框 -->
                        <div id="plupload">
                        	@if($supplier->store_logo!='')
                        	<div class="new-imgbox">
                        		<div class="images-div photos">
                        			<input class="image_name" type="hidden" name="image_name" value="{{$supplier->store_logo}}">
                    				<img width="100" height="100" src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$supplier->store_logo}}">
                    				<span id="wq" class="img-del"></span>
                        		</div>
                        	</div>
                        	@endif
                        </div>
                        <!-- 图片放置框 -->
                        <div id="uploader"></div>
                            <div style="color: #999;">建议上传120*120的图片</div>
                        </td>
                    </tr>
                    <tr>
                    	<th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th>商品发货地：</th>
                        <td>
                            <select name="store_province_id" class="input-text supplier-select">
                                <option value="0">选择省份</option>
                                <option value="2">省份</option>
                            </select>
                            <select name="store_city_id" class="input-text supplier-select">
                                <option value="0">选择市</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <input type="button" class="Preservation" onclick="Verification();" value="保存"/>
                <input type="submit" class="submitButton" style="display: none;">
            </div>
		</form>
	</div>
	@endif
    <div class="rightCon">
        <div class="wrap">
            <h2><span>概况</span><a href="{{url('auth/logout')}}" style="float: right;margin-right: 20px;color: #666;">退出账号</a></h2>
            <div class="box">
                <h3>订单统计</h3>
                <div class="statisNum">
                    <div>￥<b>{{$data['order_sale']}}</b></div>
                    <div>今日营业额</div>
                </div>
                <div class="statisNum">
                    <div><b>{{$data['order_today']}}</b></div>
                    <div>今日订单额</div>
                </div>
                <div class="statisNum">
                    <div><b>{{$data['payed_order']}}</b></div>
                    <div>待发货</div>
                </div>
                <div class="statisNum">
                    <div><b>{{$data['after_order']}}</b></div>
                    <div>售后订单</div>
                </div>
                <div class="statisNum">
                    <div><b>{{$data['all_order']}}</b></div>
                    <div>全部订单</div>
                </div>
            </div>
            <div class="box">
                <h3>商品统计</h3>
                <div class="statisNum">
                    <div><b>{{$data['goods_sale']}}</b></div>
                    <div>出售中</div>
                </div>
                <div class="statisNum">
                    <div><b>{{$data['goods_num']}}</b></div>
                    <div>已售罄</div>
                </div>
                <div class="statisNum">
                    <div><b>{{$data['goods_down']}}</b></div>
                    <div>已下架</div>
                </div>
                <div class="statisNum">
                    <div><b>{{$data['goods_check']}}</b></div>
                    <div>待审核</div>
                </div>
                <div class="statisNum">
                    <div><b>{{$data['goods_return']}}</b></div>
                    <div>已驳回</div>
                </div>

            </div>
        </div>

    </div>
    </div>
    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{asset('lib/pljs/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/pljs/plupload.full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/pljs/jquery.ui.plupload/jquery.ui.plupload.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/pljs/jquery.plupload.queue/jquery.plupload.queue.js')}}"></script>
    <script type="text/javascript">
        <?php $timestamp = time();?>
        $(function () {
        	
        	$(".deletePopup").click(function(){
        		$(".setDiv").remove();
        	});
            //获取省级
            province();
            function province(){
            	var html = '<option value="0">选择省份</option>';
                $.get('/supplier/getCity/1',function(json){
                    $.each(json,function(k,v){
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    $('[name=store_province_id]').html(html);
                });
            }
            //获取市级
            $('[name=store_province_id]').bind('change',function(){
                var province = $(this).val();
                var html = '<option value="0">选择市</option>';
                $.get('/supplier/getCity/'+province,function(json){
                    $.each(json,function(k,v){
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    $('[name=store_city_id]').html(html);
                });
            });
            //店铺头LOGO上传

            //去掉queue时就变成预览上传，无法显示效果
          	 $("#uploader").pluploadQueue({
          	  //设置类型
          	  runtimes : 'html5,flash,silverlight',
          	  //设置上传的url
          	  //url : 'http://up.qiniu.com/',
          	  url : '{{url('/supplier/uploadPlupLoad')}}',
              multi_selection:false,
          	  multipart: true,
          	  auto_start : true,
          	  unique_names:true,
          	  //设置post传给七牛的token
          	  multipart_params: {
          	   
          	  },
          	   // 设置大小
          	  resize : 
          	  {
              	  //width : 120, //指定压缩后的图片宽度
              	  //height :120, //指定压缩后的图片的高度
              	  //crop:false,//是否对图片进行裁剪
              	  quality : 60 //压缩后图片的质量，只对jpg格式的图片有效，默认为90 
          	  }, 
          	  //修改post字段为七牛的file
          	  file_data_name: 'file',
          	  //视图激活
          	  views: {
          	      list: true,
          	      thumbs: true, // Show thumbs
          	      active: 'thumbs'
          	  },
          	  //启用将文件拖放到小部件上的功能（目前只支持HTML5）
          	  dragdrop: true,

          	  //设置一些限制
          	  filters : {
          	   // 设置大小
          	   max_file_size : '1000mb',
          	   // 允许上传的类型
          	   mime_types: [
          	    {title : "Image files", extensions : "jpg,gif,png,jpeg"},
          	    {title : "RAR files", extensions : "rar,zip,tar.gz"}
          	   ]
          	  },
          	  // 设置Flash的路径
          	  flash_swf_url : '{{asset('lib/pljs/Moxie.swf')}}',
          	  // 设置Silverlight的路径
          	  silverlight_xap_url : '{{asset('lib/pljs/Moxie.xap')}}',
          	  
          	 });

      	     //上面的pluploadQueue改为plupload时，此处的 pluploadQueue也要改为plupload
      	     var uploader = $('#uploader').pluploadQueue();
      	            //绑定FIlesAdded这个方法 具体的好多方法大家可以看官方的API 单一文件上传的方法
        	        uploader.bind('FilesAdded',function(up,files){
      	             //获取文件名称 这个是单一的 如果多文件需要循环上传
      	               var filename = files[0].name;
      	               var filedata = filename.split(".");
      	               var type = filedata[filedata.length-1];
      	               up.settings.multipart_params.key="<?php echo date('Ymd-His') . '-' . rand(10000,99999);?>"+"."+type;
      	               this.start(); 
      	             });
      	      uploader.bind('FileUploaded',function(uploader,file,responseObject){
      	    	  var data = JSON.parse(responseObject.response);	          
      	          $('.plupload_content').html('');
      	          var imgbox = $(".new-imgbox").html();
      	          if(!imgbox){
      	              $('#plupload').html('<div class="new-imgbox"></div>');
      	          } 
      	          $('.new-imgbox').html('<div class="images-div photos"><input class="image_name" type="hidden" name="image_name" value="'+data.img+'"><img width="100" height="100" src="{{env("IMAGE_DISPLAY_DOMAIN")}}' +data.img+'" /><span id="wq" class="img-del"></span></div>');
      	          deleteimg();
      	      });
   
        });


        function deleteimg(){
          	 $(".img-del").unbind().click(function(){
              	 $(this).parent().remove();
            	 })
        }
        
        
        function Verification(){
        	var name = $('[name=name]').val();
        	var mobile = $('[name=mobile]').val();
        	var store_name = $('[name=store_name]').val();
        	var store_logo = $('.image_name').val();
        	var store_province_id = $('[name=store_province_id] option:selected').attr("value");
        	var store_city_id = $('[name=store_city_id] option:selected').attr("value");
        	var pattern = /^1[34578]\d{9}$/; 
        	if(!name){
        		layer.msg("请填写姓名");
        		return false;
        	}else if(!pattern.test(mobile)){
        		layer.msg("请填写正确的手机号")
        		return false;
        	}else if(!store_name){
        		layer.msg("请填写店铺名")
        		return false;
        	}else if(!store_logo){
        		layer.msg("请上传店铺logo")
        		return false;
        	}else if(store_province_id == "0"){
        		layer.msg("请选择省份")
        		return false;
        	}else if(store_city_id == "0"){
        		layer.msg("请选择市区")
        		return false;
        	}
        	$(".submitButton").click();
        }
    </script>
@stop
