@extends('layout_pop')
@section("content")
    <style>
        .img-del {
            display: inline-block;
            width: 13px;
            height: 13px;
            position: relative;
            top: -102px;
            left: 88px;
            cursor: pointer;
            background: url("{{asset('images/acrossTab-close.png')}}") -91px -13px;
        }
        /* 添加图片图标样式 */
        .fileUploadIcon{
            display: inline-block;
            width: 100px;
            height: 100px;
            background: transparent url("/images/file_icon_03.png") repeat scroll 0% 0% / 100px auto;
            border:none;
        }
        /* 图片上传进度样式控制 */
        .ul_pics{ float:left;margin:0 10px 0 10px ;float:left;}
        .rightCon .form #ul_pics li{padding-bottom:18px;}
        .ul_pics li{float:left; margin:0px; padding:0px; position:relative; list-style-type:none;border:1px solid #eee; width:100px; height:100px;} .ul_pics li img{width:100px; height:100px;}
        .progress{position:relative;margin-top:52px; background:#eee;width:100px;}
        .bar {background-color: green; display:block;width:0%; height:15px;}
        .percent{position:absolute; height:15px; top:-18px;text-align:center; display:inline-block; left:0px; width:80px; color:#666;line-height:15px; font-size:12px; }
        /* 图片上传进度样式控制 */
    </style>
    <link rel="stylesheet" href="{{asset('lib/pljs/jquery-ui.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('lib/pljs/jquery.plupload.queue/css/jquery.plupload.queue.css')}}" type="text/css" />
    <form action="/wx/reply/add" id="form" method="post">
    <div style="padding: 20px 20px ">
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td colspan="2">
                    关键字：<input id="txt" type="text" datatype="*" class="input-text radius" style="width:40%" name="keyword">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <ul id="ul_pic" class="ul_pics clearfix">
                    </ul>
                    <div style="margin-left:10px;float:left;">
                        <button id="coverbrowse" class="fileUploadIcon"></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" id="remark" name="remark" datatype="*" value="">
                    <input type="hidden" id="type" name="type" datatype="*" value="">
                    <input type="hidden" id="create_time" name="create_time" datatype="*" value="">
                    <input type="hidden" id="media_id" name="media_id" datatype="*" value="">
                   <input type="submit" class="input-text radius" style="width:30%" name="display_order" value="确定">
                </td>
                <td>
                    <input onclick="b()" type="button" class="input-text radius" style="width:30%" name="display_order" value="取消">
                </td>
            </tr>
        </table>
    </div>
    </form>
@endsection
@section("javascript")
    <script type="text/javascript" src="{{asset('lib/plupload/plupload.full.min.js')}}"></script>
    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
    <script>
        function b(){
            layer_close();
        }

        var uploader = new plupload.Uploader({
            multiple:false,
            browse_button : 'coverbrowse', //触发文件选择对话框的按钮，为那个元素id
            url : '{{url('/wx/upload')}}', //服务器端的上传页面地址
            resize: {
                quality: 60,
            },
            flash_swf_url : '{{asset('lib/plupload/Moxie.swf')}}',
            silverlight_xap_url : '{{asset('lib/plupload/Moxie.xap')}}'
        });
        uploader.init();
        uploader.bind('FilesAdded',function(uploader,files){
            var li = "<li id='singleupload'><div class='progress'><span class='bar'></span><span class='percent'>已上传  0%</span></div></li>";
            $("#ul_pic").html(li);
            uploader.start();
        });
        uploader.bind('UploadProgress',function(uploader,file){
            var percent = file.percent;
            $("#singleupload").find('.bar').css({"width":percent + "%"});
            $("#singleupload").find(".percent").text("已上传"+ percent + "%");
        });
        uploader.bind('FileUploaded',function(uploader,file,responseObject){
            var data = JSON.parse(responseObject.response);
            console.log(data);
            var str = '';
            str +='<img src="'+data.img+'" alt="" width="100" height="100" class="upload-img">'+
                    '<i class="img-del"></i>;'
            $("#singleupload").html(str);
            $('#remark').val(data.remark);
            $('#type').val(data.type);
            $('#create_time').val(data.create_time);
            $('#media_id').val(data.media_id);
            $(".img-del").click(function(){
                $(this).parent().remove();
                $('#remark').val('');
                $('#type').val('');
                $('#create_time').val('');
                $('#media_id').val('');
            })
        });

        $(function(){
            $("#form").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'no'){
                        layer.msg('关键字重复!',{icon:6,time:1000});
                    }else{
                        layer.msg('已添加!',{icon:1,time:1000});
                        parent.location.replace(parent.location.href);
                    }
                }
            });
        })


    </script>
@endsection