@extends('layout_pop')
    <style>
        #all{
            margin-left: 1%;
        }
        .abc>div {
            float: left;
        }
    </style>
@section("content")
    <link rel="stylesheet" type="text/css" href="{{ asset('lib/uploadify/uploadify.css') }}">
    <div id="all">
        <form id="form" action="/conf/confThemes/{{$ConfThemes->id}}}}" method="post">
            <table class="table table-border table-bordered table-hover">
                <tr>
                    <td>所属应用:
                        <span class="select-box" style="width: 51%">
                            <select class="select" size="1" name="location" >
                                <option value="0" @if($ConfThemes->location == 0) selected @endif>微商城</option>
                                <option value="1" @if($ConfThemes->location == 1) selected @endif>app导游端</option>
                            </select>
                        </span>
                    </td>
                    <td>所属分馆:
                        <span class="select-box" style="width: 51%">
                            <select class="select" size="1" name="pavilion_id" >
                                @foreach($ConfPavilions as $ConfPavilion)
                                    @if($ConfThemes->pavilion_id == $ConfPavilion->id)
                                        <option selected value="{{$ConfPavilion->id}}" >{{$ConfPavilion->name}}</option>
                                    @else
                                        <option value="{{$ConfPavilion->id}}" >{{$ConfPavilion->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                         </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="color: red;font-size: large;text-align: center">*</span>专题名称：<input type="text" class="input-text radius" style="width:51%" name="name" datatype="*" value="{{$ConfThemes->name}}">
                    </td>
                    <td>
                        排序:<input type="number" class="input-text radius" style="width:51%" name="display_order" value="{{$ConfThemes->display_order}}">
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="color: red;font-size: large;text-align: center">*</span>属性：
                        <select class="select" datatype="*" size="1" name="url_type" style="width:51%" >
                            <option value="0" {{($ConfThemes->url_type == 0) ? 'selected' : ''}} >商品链接</option>
                            <option value="1" {{($ConfThemes->url_type == 1) ? 'selected' : ''}}>商品ID</option>
                        </select>
                    </td>
                    <td>
                        <span style="color: red;font-size: large;text-align: center">*</span>商品链接或id：<input type="text" value="{{$ConfThemes->url}}" datatype="*" class="input-text radius" style="width:51%" name="url">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="img">封面图：
                            <input type="file" id="cover">
                            <input type="hidden" name="cover" value="{{$ConfThemes->cover}}">
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfThemes->cover}}" alt="" width="50%" height="50%">;
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        专题描述：<textarea maxlength="35" style="width: 500px;height: 45px;" name="content">{{$ConfThemes->content}}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="button" class="input-text radius f-l" value="取消" onclick="b()" style="width:40%" >
                    </td>
                    <td>
                        <input type="submit"  class="btn btn-primary radius f-r" value="确定" style="width:40%" >
                    </td>
                </tr>
            </table>
        </form>
    </div>

@endsection
@section("javascript")

    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
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
                'fileTypeExts': '*.gif; *.jpg; *.png; *.jpeg',
                'height': 30,
                'width': 150,
                'multi':false,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    var addimg = '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}' + data.img + '" alt="" width="50%" height="50%">';
                    $('#img>img').remove();
                    $('#img').append(addimg);
                    $("[name= cover]").val(data.img);
                },
                'onUploadError' : function(file, errorCode, errorMsg, errorString) {
                    alert('图片 ' + file.name + ' 不能上传原因是: ' + errorString);
                },
                'onCancel' : function(file) {
                    alert('图片 ' + file.name + ' 上传被取消.');
                }
            });
        })

        $(function(){
            $("#form").Validform({
                tiptype:function(i){

                },
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'yes') {
                        layer.msg(data.msg);
                        parent.location.replace(parent.location.href);
                    }else {
                        layer.msg(data.msg);
                    }
                }
            });
        });

        function b(){
            layer_close();
        }

    </script>
@endsection