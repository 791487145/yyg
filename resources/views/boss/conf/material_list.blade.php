@extends('layout')
    <style>
        .imgbut{
		margin-left: 10px;
	   }
	   #uploader_container{height: 0;display: none;} 

    	.plupload_buttons{
    		margin-left:50px;
    	}
        .all>div{
            float: left;
        }
        .all{
            width:96%;
            margin-left: 3%;
            margin-top: 3%;
        }
        .a{
            
            width: 44%;
            margin-right: 5%;
        }
        .b{
            border: 1px solid #ccc;
            width: 40%;
            padding:20px;
        }
        .img-del {
            display: inline-block;
            width: 13px;
            height: 13px;
            position: relative;
            top: -47px;
            left: -5px;
            cursor: pointer;
            background: url(/images/acrossTab-close.png) -91px -13px;
        }
        .images-div {
            display: inline-block;
            position: relative;padding-bottom:30px;background:none;
        }
        div{
            margin-bottom: 10px;
        }

        .ba>div{
            float: left;
            margin-right: 10px;
        }
        .leftTitle{float: left;width:20%;text-align: right;padding-right:5%;}
        .leftText{float: left;width:75%;}
    </style>
@section("content")
    <link rel="stylesheet" href="{{asset('lib/pljs/jquery-ui.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('lib/pljs/jquery.plupload.queue/css/jquery.plupload.queue.css')}}" type="text/css" />
    @if(count($errors)>0)
        @foreach($errors->all() as $value)
            {{$value}}
        @endforeach
    @endif
<div class="all">
   <form method="get" action="/conf/materials">
        <div class="text-l" style="margin: 20px 5px">
                            商品名称：
               <input type="text" name="goods_name" value="{{$goods_name}}" placeholder="  商品名称" style="width:250px" class="input-text">&emsp;
                           发布时间：
               <input type="text" name="start_time" value="{{$start_time}}" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:180px;">
                                             至
               <input type="text" name="end_time" value="{{$end_time}}" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:180px;">&emsp;
                          供应商：
               <select name="supplierid" class="input-text" style="width:auto;">
                   <option value="0">全部</option>
                   @foreach($supplierbases as $supplier)
                   <option value="{{$supplier->id}}" @if($supplier->id == $supplierid) selected="selected" @endif>{{$supplier->name}}</option>
                   @endforeach
               </select>&emsp;     
               <button  class="btn btn-success" type="submit"><i class="Hui-iconfont"></i> 筛选</button>  
        </div>
   </form>
   <div class="a">
       <form action="/conf/material" method="post" id="form">
       		<div class="oh">
	            <h4>发布素材 <input type="submit" value="发布" class="btn btn-success radius f-r"></h4>
           	</div>
           	<div class="pd-20" style="border: 1px solid #ccc;">
           		<div class="oh">
           			<div class="leftTitle">添加关联商品：</div>
	           		<select class="leftText" name="id" size="1" style="width: 50%;padding:6 10px;">
	           			<option value="" selected>请选择</option>
	           			@foreach($GoodBases as $goodBase)
	                        <option value="{{$goodBase->id}}"> {{$goodBase->id}}  {{$goodBase->title}}</option>
	                    @endforeach
					</select>
           		</div>
				<div class="oh">
					<div class="leftTitle">请输入内容：</div>
					<div class="leftText" >
						<div class="pos-r">
							<textarea id="txt" name="content" cols="20%" rows="10%" class="textarea radius"></textarea>
							<div class="pos-a" style="bottom: -10px;right: 5px;"><span id="word">0</span>/100</div>
						</div>
		               <script language="javascript" type="text/javascript">
		                   
		               </script>
	               </div>
				</div>
				<div class="oh">
					<div class="leftTitle">请添加图片：</div><br>
					<!-- 图片放置框 -->
                    <div id="plupload"></div>
                    <!-- 图片放置框 -->
                    <div class="new-imgbox"></div>
                    <div id="uploader"></div>
				</div>
				<div class="oh">
					<div class="leftTitle">&nbsp;</div>
					<div class="leftText" id="imglist">
		            </div>
				</div>
           </div>
       </form>
   </div>
   <div class="b">
   		<h4>素材记录</h4>
   		<br />
   		<div class="line"></div>
        @foreach($GoodsMaterialBases as $GoodsMaterialBase)
        <div>发布时间：
            <span>
                {{$GoodsMaterialBase->created_at}}
            </span>
            <span style="padding: 70px">
                <a title="删除" href="javascript:;" onclick="confirm_action('确定要删除素材吗？','/conf/materials/{{$GoodsMaterialBase->id}}')" class="ml-5" style="text-decoration:none;color:#33CCFF;">
                    <i class="Hui-iconfont">&#xe6e2;</i>删除
                </a>
                <a title="修改" href="javascript:;" onclick="edit( '编辑素材', '/conf/edit/{{$GoodsMaterialBase->id}}')" class="ml-5" style="text-decoration:none;color:#33CCFF;">
                    <i class="Hui-iconfont">&#xe60c;</i>修改
                </a>
            </span>
        </div>
        <div>{{$GoodsMaterialBase->content}}</div>
        <div class="ba oh">
           @foreach($GoodsMaterialBase->tmp as $image_name)
           <div class="photos">
               <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$image_name}}" class="radius" width="80px" height="80px">
           </div>
          @endforeach
        </div>
        <div class="oh photos">
            <img class="f-l" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$GoodsMaterialBase->cover}}" class="radius" width="80px" height="80px">
           	<span class="f-l" style="width:70%;margin-left: 10px;">{{$GoodsMaterialBase->title}}</span>
        </div>
        <br />
        <div class="line"></div>
        @endforeach
        <div>
           <?php echo $GoodsMaterialBases->appends(['goods_name'=>$goods_name,'start_time'=>$start_time,'end_time'=>$end_time,'supplierid'=>$supplierid])->render();     ?>
        </div>
   </div>
</div>

@endsection

@section("javascript")
<script type="text/javascript" src="{{asset('lib/pljs/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('lib/pljs/plupload.full.min.js')}}"></script>
<script type="text/javascript" src="{{asset('lib/pljs/jquery.ui.plupload/jquery.ui.plupload.js')}}"></script>
<script type="text/javascript" src="{{asset('lib/pljs/jquery.plupload.queue/jquery.plupload.queue.js')}}"></script>
<script type="text/javascript" src="http://validform.rjboy.cn/Validform/v5.3.2/Validform_v5.3.2_min.js"></script>
<script type="text/javascript">
$(function() {
	//去掉queue时就变成预览上传，无法显示效果
	 $("#uploader").pluploadQueue({
	  //设置类型
	  runtimes : 'html5,flash,silverlight',
	  //设置上传的url
	  //url : 'http://up.qiniu.com/',
	  url : '/conf/uploadMaterial',
	  
	  multipart: true,
	  auto_start : true,
	  unique_names:true,
	  //设置post传给七牛的token
	  multipart_params: {
	   
	  },
	   // 设置大小
	  resize : 
	  {
    	  //width : 800, //指定压缩后的图片宽度
    	  //height :800, //指定压缩后的图片的高度
    	  crop:false,//是否对图片进行裁剪
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
          /* $('.plupload_content').html('');
          var imgbox = $(".new-imgbox").html();
          if(!imgbox){
              $('#plupload').append('<div class="new-imgbox"></div>');
          } */
          $imgNum = $(".images-div").length;
          if($imgNum < 9){
        	  $('.new-imgbox').append('<div class="images-div photos"><input type="hidden" name="image_name[]" value="'+data.img+'"><img width="100" height="100" src="{{env("IMAGE_DISPLAY_DOMAIN")}}' +data.img+'" /><span id="wq" class="img-del"></span></div>');
              deleteimg();  
          }else{
      	      return countNum("图片的最大上传数量为9");
          }
          
      });

});

 function deleteimg(){
	 $(".img-del").unbind().click(function(){
    	 $(this).parent().remove();
  	 })
 }

 //上传图片的提示数量的函数
 function countNum(message){
     layer.alert(message);
 }
 
function confirm_action(message,url){
	layer.confirm(message,function(index){
		$.get(url,function (data) {
			layer.msg('已删除!',{icon:1,time:1000});
			window.location.reload();
		})
	});
}

//编辑素材
function edit(title,url){
	layer_show(title,url);
}
$("#txt").keyup(function(){
	var val = $(this).val();
	var length = val.length;
	if(length<100){
		$("#word").text(100-length);
	}else{
		val = val.substring(0,100);
		$("#txt").val(val);
		$("#word").text(100);
	}
})  

//进行编辑素材回调的函数
$("#form").Validform({
    tiptype:function(data){
    },
    ajaxPost:true,
    postonce:true,
    callback:function(data){
        if(data.ret == 'yes') {
            //获取当前对象，并将内容进行更新
            var index = layer.open({
                content: data.msg,
                btn: ['确认'],
                yes: function(index, layero) {
                	//parent.location.replace(parent.location.href);
                	window.location.reload()
                },
                cancel: function() {
                    window.location.reload()
                }
            });
        } else {
            layer.alert(data.msg, {icon:2});
        }
    }
});



</script>

@endsection