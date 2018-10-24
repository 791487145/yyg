@extends('layout')
    <style>
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
    <link rel="stylesheet" type="text/css" href="{{ asset('lib/uploadify/uploadify.css') }}">
    <script src="http://static.l99.com/js/jquery/jquery-1.2.6.pack.js" type="text/javascript"></script>
    @if(count($errors)>0)
        @foreach($errors->all() as $value)
            {{$value}}
        @endforeach
    @endif
<div class="all">
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
		                    $("#txt").keyup(function(){
		                   		var lengths = $("#txt").val().length;
		                        if(lengths > 100){
		                            $("#txt").val( $("#txt").val().substring(0,100) );
		                        }
		                       $("#word").text(lengths) ;
		                    });
		               </script>
	               </div>
				</div>
				<div class="oh">
					<div class="leftTitle">请添加图片：</div>
					<div class="leftText aa">
		               <input type="file" id="img">
		            </div>
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
                <a title="删除" href="javascript:;" onclick="confirm_action('确定要删除素材吗？','/conf/materials/{{$GoodsMaterialBase->id}}')" class="ml-5" style="text-decoration:none">
                    删除<i class="Hui-iconfont">&#xe6e2;</i>
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
           <?php echo $GoodsMaterialBases->render();     ?>
        </div>
   </div>
</div>

@endsection

@section("javascript")

<script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
<script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
<script>
   <?php $timestamp = time();?>
   $(function(){
       $('#img').uploadify({
           'formData': {
               'timestamp': '<?php echo $timestamp;?>',
               'token': '<?php echo md5('unique_salt' . $timestamp);?>'
           },
           'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
           'uploader': '{{ url('/conf/upload') }}',
           'buttonText': '选择文件',
           'fileTypeDesc': 'filetypedesc',
           'fileTypeExts': '*.gif; *.jpg; *.png ;*.jpeg',
           'height': 30,
           'width': 150,
           'onUploadSuccess': function (file, data, response) {
               data = JSON.parse(data);
               var addimg = '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}' + data.img + '" alt="" style="width:80px;height:80px;margin:0 5px 5px 0">' +
                       '<input type="hidden" name="image_name[]" value="' + data.img + '">';
               $('#imglist').append(addimg);
           }
       });

   })

   function confirm_action(message,url){
       layer.confirm(message,function(index){
           $.get(url,function (data) {
               layer.msg('已删除!',{icon:1,time:1000});
               location.href = '/conf/materials';
           })
       });
   }

   layer.photos({
       photos: '.photos'
       ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
   });
</script>

@endsection