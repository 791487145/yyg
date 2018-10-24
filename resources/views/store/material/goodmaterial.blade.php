@extends('supplier')
<style>
    .imgbut{
		margin-left: 10px;
	   }
	#uploader_container{height: 0;display: none;} 
    .img-del {
        display: inline-block;
        width: 13px;
        height: 13px;
        position: relative;
        top: 0px;
        left: 98px;
        cursor: pointer;
        background: url(/images/acrossTab-close.png) -91px -13px;
    }
    .images-div {
        display: inline-block;
        position: relative;padding-bottom:30px;background:none;
    }
    .uploadify-queue{display: none;}
    .tab-del{display: block;width: 19px;height: 19px;position: relative;left: 950px;background: url("{{asset('images/icon_del.png')}}");cursor: pointer; }
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
    .topTitle{background: #e8e7e7;line-height: 56px;font-size:18px;padding-left: 20px;margin-bottom:13px;}
    .left-good-div{width:40%;background: #fff;float: left;margin-right:3%;}
    .right-good-div{width:56%;background: #fff;float: left;}
    .goodsInfoDiv{margin-bottom: 20px;width: 100%;overflow: hidden;}
    .goodsInfoDiv span{float: left;}
    .goodsInfoDiv .infoName{width: 130px;text-align: right;display: inline-block;padding-right: 5px;}
    .goodsInfoDiv .textCountNum{position: absolute;bottom:10px;right:10px;color: #999;}
    .deleteBut{float: right;cursor: pointer;}
</style>
@section('content')
<link rel="stylesheet" href="{{asset('lib/pljs/jquery-ui.css')}}" type="text/css" />
<link rel="stylesheet" href="{{asset('lib/pljs/jquery.plupload.queue/css/jquery.plupload.queue.css')}}" type="text/css" />
       <div class="rightCon">
       		<div class="topTitle">商品素材</div>
            <div class="left-good-div">
            	
                <form action="/material/addMaterial" method="post">
                	<div class="box margin-bottom10">
                		<span>发布素材</span>
                		<input type="hidden" name="supplier_id" value="{{$supplier_id}}">
                		<input class="float-right but-yes" type="submit" value="发布">
                	</div>
                    <div class="box">
                    	<div class="goodsInfoDiv">
                    		<span class="infoName">添加关联商品：</span>
                    		<span>
                    			<select class="but-style" name="goods_id">
                    			@foreach($goodsinfo as $goods)
                                    <option value="{{$goods->id}}">{{$goods->title}}</option>
                                @endforeach
                                </select>
                    		</span>
                    	</div>
                    	<div class="goodsInfoDiv">
                    		<span class="infoName">请输入内容：</span>
                    		<span style="position: relative;" class="textCount">
                                <textarea maxlength="100" data-num="100" style="width:320px;height: 120px;" name="content"></textarea>
                                <span class="textCountNum"><i>0</i>/100</span>
                    		</span>
                    	</div>
                    	<div class="goodsInfoDiv">
                    		<span class="infoName">请添加图片：</span>
                    		<span>
                                <!-- 图片放置框 -->
                                <div id="plupload">
                                    <div class="new-imgbox"></div>
                                </div>
                                <!-- 图片放置框 -->
                                <div id="uploader"></div>
                               {{-- <p class="tip">（建议尺寸：700*320像素  最多上传9张图片）</p>--}}
                    		</span>
                    	</div>
                    </div>
                </form>
            </div>
            <div class="right-good-div">
            	<div class="box">
            		<span>素材记录</span>
            	</div>
                <div class="box">
                @foreach($materialinfo as $info)
                	<div class="imgBox border-bottom padding10" id="imgBox{{$info->id}}">
                		<div class="color9 margin-bottom10">发布时间：{{$info->created_at}}<span class="deleteBut" onclick="confirm_action('确定要删除素材吗？','/material/delmaterial/{{$info->id}}')">删除</span></div>
                		<div class="margin-bottom10">{{$info->content}}</div>
                		<div class="margin-bottom10">
                		@foreach($info->img as $imgurl)
                			<img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$imgurl}}" width="100" height="100"/>
                		@endforeach	
                		</div>
                		<div class="oh">
                			<img class="float-left margin-right10" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$info->cover}}" width="50" height="50"/>
                			<span class="float-left">{{$info->title}}</span>
                		</div>
                	</div>
               @endforeach 	
                    <div class="footPage">
                        <p>共{{$totalpage}}页,{{$num}}条数据 ；每页显示{{$pagesize}}条数据</p>
                        <div class="pageLink">
                            <?php echo $materialinfo->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@stop
@section('footer')
    <link href="{{ asset('lib/umeditor/themes/default/css/umeditor.css') }}" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="{{asset('lib/pljs/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/pljs/plupload.full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/pljs/jquery.ui.plupload/jquery.ui.plupload.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/pljs/jquery.plupload.queue/jquery.plupload.queue.js')}}"></script>
    <script type="text/javascript">
    $(function() {
    	//去掉queue时就变成预览上传，无法显示效果
    	 $("#uploader").pluploadQueue({
    	  //设置类型
    	  runtimes : 'html5,flash,silverlight',
    	  //设置上传的url
    	  //url : 'http://up.qiniu.com/',
    	  url : '/material/uploadPlupLoad',
    	  
    	  multipart: true,
    	  auto_start : true,
    	  unique_names:true,
    	  //设置post传给七牛的token
    	  multipart_params: {
    	   
    	  },
    	  //设置上传的数量
    	  
    	  // 设置大小  商品素材的图片不需要指定大小
    	  resize : 
    	  {
        	  /* width : 800, //指定压缩后的图片宽度
        	  height :800, //指定压缩后的图片的高度
        	  crop:false,//是否对图片进行裁剪 */
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
           //控制图片的上传数量
           //arr = uploader.files;
           /* if(uploader.files.length>3){
       	       return countNum('图片的最大上传数量为3');
           } */
           this.start(); 
         });   
	             
	      uploader.bind('FileUploaded',function(uploader,file,responseObject){
		      var imgNum = $(".images-div").length;
	          if(imgNum<9){
	        	  var data = JSON.parse(responseObject.response);
		          $('.new-imgbox').append('<div class="images-div photos"><input type="hidden" name="image_name[]" value="'+data.img+'"><img width="100" height="100" src="{{env("IMAGE_DISPLAY_DOMAIN")}}' +data.img+'" /><span id="wq" class="img-del"></span></div>');
		          deleteimg();  
		      }else{
		    	  return countNum('图片的最大上传数量为9');  
			  }
	          
	      });

    	});
    
         function deleteimg(){
        	 $(".img-del").unbind().click(function(){
            	 $(this).parent().remove();
          	 })
         }
        //删除素材的函数 
    	function confirm_action(message,url){
    		layer.confirm(message,function(index){
    			$.get(url,function (data) {
    				layer.msg('已删除!',{icon:1,time:1000});
    				window.location.reload();
    			})
    		});
    	}

        //上传图片的提示数量的函数
        function countNum(message){
            layer.alert(message);
        }
    	
        //删除素材的函数，已作废
        function delmaterial(id){
            if(confirm('你确定删除本条素材吗？')){
                $.post("{{url('material/delmaterial')}}",{'id':id},function(){
                   var uid = '#imgBox'+id;
                   //alert(uid);
                   $(uid).remove();
                })
            }else{
                return false;
            }
        }


    	
    		   
    </script>
    
    
@stop
