@extends('layout_pop')
@section("content")
<style>
    #all{
        margin-left: 1%;
    }
    .abc>div {
        float: left;
    }
    .leftTitle{float: left;width:20%;text-align: right;padding-right:5%;}
    .leftText{float: left;width:75%;}
    
    .images-div {
        display: inline-block;
        position: relative;padding-bottom:30px;background:none;margin-right:2px;width: 100px;
    }
    .img-del{
	float: left;background: url("{{asset('images/acrossTab-close.png')}}") -91px -13px;
	cursor: pointer;width: 13px;height: 13px;
	display: inline-block;position: absolute;left:83px;top:2px;
	}
    /* 添加图片图标样式 */
    .fileUploadIcon{
        display: inline-block;
        width: 100px;
        height: 100px;
        background: transparent url("{{asset('images/file_icon_03.png')}}") repeat scroll 0% 0% / 100px auto;
        border:none;
    	 float:left;
    	margin-left:100px;
    }
    /* 图片上传进度样式控制 */
    .ul_pics{ float:left;} 
    .rightCon .form #ul_pics li{padding-bottom:18px;}
    .ul_pics li{float:left; margin:0px; padding:0px; position:relative; list-style-type:none;border:1px solid #eee; width:100px; height:100px;} .ul_pics li img{width:100px;height:100px;}
    .progress{position:relative;margin-top:50px; background:#eee;} 
    .bar {background-color: green; display:block;width:0%; height:15px; } 
    .percent{position:absolute; height:15px; top:-18px;text-align:center; display:inline-block; left:0px; width:80px; color:#666;line-height:15px; font-size:12px; }
    .progress{width:100px;overflow:hidden;}
    /* 图片上传进度样式控制 */
    
</style>
    <div id="all">
        <form id="form" action="/conf/update" method="post">
           	<div>
           		<div class="oh pd-20">
           			<div class="leftTitle">关联商品：</div>
	           		<input type="text" style="width:375px;padding: 4px 6px;border:1px solid #e8e8e8;" value="{{$goodname}}" readonly="readonly">
	           		<input type="hidden" name="id" value="{{$id}}" readonly="readonly">
           		</div>
				<div class="oh pd-20">
					<div class="leftTitle">请输入内容：</div>
					<div class="leftText" >
						<div class="pos-r">
							<textarea id="txt" name="content"  class="textarea radius" style="width:375px;">{{$content}}</textarea>
							<div class="pos-a" style="bottom:0px;right: 200px;"><span id="word">0</span>/100</div>
						</div>
	               </div>
				</div>
				<div class="oh pd-20">
           			<div class="leftTitle">请添加图片：</div>
	           		    <div class="fileResult oneFileResult">
	           		    <input type="hidden" id="goodmaterialid" value={{$id}}>
                    	<span class="fileResultbox" style="background: none;float:left;margin: 20px 0 0 100px;width:540px">
                    		@if(!empty($GoodsImages))
                    			@forelse($GoodsImages as $key => $image)
                    				<div class="images-div photos" data-id="{{$image->id}}">
                    					<img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$image->image_name}}" alt="" width="100" height="100" class="upload-img">
                    					<i class="img-del" data-id="{{$image->id}}" image-name="{{$image->image_name}}"></i>
                    					<input type="hidden" name="image_name[]" value="{{$image->image_name}}">
                    				</div>
                    				@empty
                    			@endforelse
                    		@endif
                    	</span>
                		@if(!empty($GoodsImages))
                		    <div id="coverimg" class="fileUploadIcon" @if($GoodsImages->count() >= 9)style="display: none;" @endif></div> 
                		@else 
                		    <div id="coverimg" class="fileUploadIcon"></div>    
                		@endif
                    </div>
	           		<div class="leftText">
	           		</div>
           		</div>
				<div style="display:inline-block;margin-left:200px;">
				    <input style="margin-right: 20px;" type="submit"  class="btn btn-success radius" value="确定">
                    <input type="button" class="btn btn-default radius" value="取消" onclick="b()">
				</div>
        </form>
    </div>

@endsection
@section("javascript")
<script type="text/javascript" src="{{asset('lib/plupload/plupload.full.min.js')}}"></script>
    <script>
    //关闭编辑素材的弹窗
    function b(){
        layer_close();
    }
    autolength();
    function autolength(){
    	var val = $("#txt").val();
    	var length = val.length;
    	$("#word").text(100-length);
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
    
    //删除图片的函数
    deleteimg();
    function deleteimg(){
        //.unbind()
      	 $(".img-del").click(function(){
       		$("#coverimg").show();
       		var id   = $('#goodmaterialid').val();
       		var name = $(this).attr('image-name');
       	    $.post('/conf/materialimgdel',{'id':id,'name':name})       		
          	$(this).parent().remove();
         })
    }
    
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
                            //window.history.go(index);
                    	parent.location.replace(parent.location.href);
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

    $(function(){
    	var uploader = new plupload.Uploader({
            browse_button : 'coverimg', //触发文件选择对话框的按钮，为那个元素id
            url : '{{url('/conf/uploadMaterial')}}', //服务器端的上传页面地址
            //multi_selection: true,
            resize: {
          	    quality: 60,
          	},
            flash_swf_url : '{{asset('lib/plupload/Moxie.swf')}}', 
            silverlight_xap_url : '{{asset('lib/plupload/Moxie.xap')}}' 
        });    

        //在实例对象上调用init()方法进行初始化
        uploader.init();
        
        uploader.bind('FilesAdded',function(uploader,files){
        	var imgNum = $(".fileResultbox .images-div").length;
        	var filesLength = files.length;
        	var li = '';
        	for(var i=0;i<filesLength;i++){
            	if(i<9-imgNum){
         		 li += "<div id='" + files[i].id + "' class='images-div photos'><div class='progress'><span class='bar'></span><span class='percent'>已上传  0%</span></div></div>";
            	}
            }
            $(".fileResultbox").append(li);
          	uploader.start(); 
        });

        //显示上传进度条
        uploader.bind('UploadProgress',function(uploader,file){
        	var percent = file.percent; 
       	    $("#" + file.id).find('.bar').css({"width":percent + "%"}); 
       	    $("#" + file.id).find(".percent").text("已上传"+ percent + "%");  
        }); 
        //上传完成后执行的操作
        uploader.bind('FileUploaded',function(uploader,file,responseObject){
        	var data = JSON.parse(responseObject.response);
        	var imgurl = "{{env("IMAGE_DISPLAY_DOMAIN")}}";
        	//判断1：1
        	$("body").append('<img  id="testimg" src="" style="display:none">');
        	$('#testimg').attr('src','{{env('IMAGE_DISPLAY_DOMAIN')}}/'+data.img); 
            $('#testimg').one('load',function() {
                /* var imgWidth = this.width;
                var imgHeight = this.height;
                if (imgWidth !== imgHeight ) {
                    $("#"+file.id).remove();
                    layer.confirm('请上传1:1尺寸图片');
                } else { */
                	var str = '';
                	var imgNum = $(".fileResultbox .images-div").length;
        	          if(imgNum<10){
        	          		var str = '';
                                str +='<img src="'+imgurl+'/'+data.img+'" alt="" width="100" height="100" class="upload-img">'+
                                '<i class="img-del" data-id='+data.id+' image-name='+data.img+'></i>'+
                                '<input type="hidden" name="image_name[]" value="'+data.img+'">'
        		            $("#" + file.id).html(str);
        		        	deleteimg(); 
        		          if(imgNum==9){
        		        	  $("#coverimg").hide();
            		      } 
        		      }else{
            		      $("#coverimg").hide();
        		    	  layer.msg('图片的最大上传数量为9');
        			  }
        	          $('.radio input').click(function(){
                    	$(this).parent().parent().prependTo(".fileResultbox");
                      }) 
                  /* } */
            }); 
        });
    	
    })
    </script>
@endsection