@extends('layout')
@section("content")
    <link rel="stylesheet" type="text/css" href="{{ asset('lib/uploadify/uploadify.css') }}">
<div class="page-container">
    <form class="form form-horizontal" id="form" action="/conf/confImgnoticehandle">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>图片标题：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" name="title" class="input-text" value="" placeholder="" id="title" >
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>URL：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" name="url" class="input-text" value="" placeholder="" id="url" >
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">图片摘要：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="content" id="content" cols="" rows="" class="textarea" placeholder="输入图片内容"></textarea>
                <p class="textarea-numberbar"><em class="textarea-length">0</em>/200</p>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">封面图：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="abf">
                    <div id="coverImg">
                        <input type="file" id="cover">
                        <input type="hidden" id="img" name="cover" value="">
                    </div>
                </div>
            </div>
        </div>


        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <input type="button" onclick="check()"  class="btn btn-primary radius" value="提交">
                <input type="submit" id="submit" style="display: none;">
                <button onclick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
            </div>
        </div>
    </form>
</div>
@endsection


@section("javascript")
    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('lib/validform/5.3.2/validform.js') }}" type="text/javascript"></script>
    <script>
        <?php $timestamp = time();?>
        $(function(){
            $('#cover').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/conf/upload') }}',
                'buttonText': '选择文件',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 30,
                'width': 150,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    var addimg = '<img src="{{env('IMAGE_DISPLAY_DOMAIN')}}' + data.img + '" alt="" width="10%" height="10%">';
                    $('#coverImg').append(addimg);
                    $("[name= cover]").val(data.img);
                }
            });
        })

    </script>
    <script>
    $("#content").on("keyup change",function(){
        var num = 200;
        var val = $(this).val();
        if(val.length>200){
            valnNew =val.substring(0,num);
            $("#content").val(valnNew);
            $(".textarea-length").css("color","red");
        }
        $(".textarea-length").text(val.length);
    });

        function check(){
            var title = $("#title").val();
            var url = $("#url").val();
            var content = $("#content").val();
            var cover = $("#coverImg img").attr('src');
            if(title == ''){
                layer.msg('图片标题不能为空');
                return false;
            }
            if(url == ''){
                layer.msg('URL不能为空');
                return false;
            }
            if(content == ''){
                layer.msg('图片描述不能为空');
                return false;
            }
            if(!cover){
                layer.msg('图片不能为空');
                return false;
            }
            $("#submit").click();


        }
    $(function(){
         $("#form").Validform({
            tiptype:2,
            ajaxPost:false,
            postonce:true,
            callback:function(data){
              parent.location.replace(parent.location.href);
            }
        });
    });
    </script>
    <script>

        function b(){
            layer_close();
        }

    </script>
    <script>
        function a(title,url){
            $.get(url,function(data){
                window.location.reload();
            });
        }

        function confirm_action(message,url){
            layer.confirm(message,function(index){
                $.get(url,function (data) {
                    window.location.reload();
                })
            });
        }
    </script>
    @endsection

