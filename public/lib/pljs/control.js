/**
 * 
 */

$(function() {
	//去掉queue时就变成预览上传，无法显示效果
	 $("#uploader").pluploadQueue({
	  //设置类型
	  runtimes : 'html5,flash,silverlight',
	  //设置上传的url
	  url : '/conf/upload',
	  
	  multipart: true,
	  auto_start : true,
	  unique_names:true,
	   // 设置大小
	  resize : 
	  {
	  width : 100, //指定压缩后的图片宽度
	  height : 100, //指定压缩后的图片的高度
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
	  flash_swf_url : "{{asset('lib/pljs/Moxie.swf')}}",
	  // 设置Silverlight的路径
	  silverlight_xap_url : "{{asset('lib/pljs/Moxie.xap')}}",
	  
	 });

	     //上面的pluploadQueue改为plupload时，此处的 pluploadQueue也要改为plupload
	     var uploader = $('#uploader').pluploadQueue();  // 取得上传队列
		 //alert(uploader);
	           //绑定FIlesAdded这个方法 具体的好多方法大家可以看官方的API 单一文件上传的方法
	           //文件选择后上传
	           //uploader.bind('FilesAdded', autoupload);
	           uploader.bind('FilesAdded',function(up,files){
	             //获取文件名称 这个是单一的 如果多文件需要循环上传
	               var filename = files[0].name;
	               var filedata = filename.split(".");
	               var type   = filedata[filedata.length-1];
	               up.settings.multipart_params.key="<?php echo date('Ymd-His') . '-' . rand(10000,99999);?>"+"."+type;
	               this.start(); 
	             });
	      uploader.bind('FileUploaded',function(uploader,file,responseObject){
	    	  var data = JSON.parse(responseObject.response);
	          $('.plupload_content').html('');
	          var imgbox = $(".new-imgbox").html();
	          if(!imgbox){
	              $('#plupload').append('<div class="new-imgbox"></div>');
	          }
	          $('.new-imgbox').append('<input type="hidden" name="image_name[]" value="'+data.img+'"><img src="{{env("IMAGE_DISPLAY_DOMAIN")}}'+data.img+'" class="imgbut" onclick="return deleteimg(this)"/>');
	          
	      });

	});


	function deleteimg(obj){
		//服务器端的图片url路径
		imgurl = obj.src;
		obj.style.display = 'none';
		obj.remove();
	}