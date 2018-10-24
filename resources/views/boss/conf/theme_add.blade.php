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
        <form id="form" action="/conf/confTheme" method="post">
            <table class="table table-border table-bordered table-hover">
                <tr>
                    <td>所属应用:
                        <span class="select-box" style="width: 51%">
                            <select class="select" size="1" name="location" >
                                <option value="0" >微商城</option>
                                <option value="1" >app导游端</option>
                            </select>
                        </span>
                    </td>
                    <td>所属分馆:
                        <span class="select-box" style="width: 51%">
                            <select class="select" size="1" name="pavilion_id" >
                                @foreach($ConfPavilions as $ConfPavilion)
                                    <option value="{{$ConfPavilion->id}}" >{{$ConfPavilion->name}}</option>
                                @endforeach
                            </select>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="color: red;font-size: large;text-align: center">*</span>
                        专题名称：<input type="text" class="input-text radius" datatype="*" style="width:51%" name="name">
                    </td>
                    <td>
                        排序：<input type="number" class="input-text radius" style="width:51%" name="display_order">
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="color: red;font-size: large;text-align: center">*</span>属性：
                        <select class="select" size="1" datatype="*" name="url_type" style="width:51%" >
                            <option value="0" >链接</option>
                            <option value="1" >商品ID</option>
                        </select>
                    </td>
                    <td>
                        <span style="color: red;font-size: large;text-align: center">*</span>商品链接或id：<input type="text" class="input-text radius" datatype="*" style="width:51%" name="url">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="img">封面图：<span style="color: red">首页下方精选封面图尺寸：700*320px</span>
                            <input type="file" id="cover">
                            <input type="hidden" name="cover" value="">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        专题描述：<textarea maxlength="35" style="width: 500px;height: 45px;" name="content"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit"  class="input-text radius" value="确定" style="width:51%" >
                    </td>
                    <td>
                        <input type="button" class="input-text radius" value="取消" onclick="b()" style="width:51%" >
                    </td>
                </tr>
            </table>
        {{--<div class="abc cl">
            <div class="abd">
                所属分馆：
                <span class="select-box" style="width: 51%">
                    <select class="select" size="1" name="pavilion_id" >
                        @foreach($ConfPavilions as $ConfPavilion)
                            <option value="{{$ConfPavilion->id}}" >{{$ConfPavilion->name}}</option>
                        @endforeach
                    </select>
                </span>
            </div>
            <div class="abe">排序：<input type="text" class="input-text radius" style="width:51%" name="display_order"></div>
        </div>

            <div class="abc">
                <div class="abd">专题名称：<input type="text" class="input-text radius" style="width:51%" name="name"></div>
                <div class="abe">
                    url类型：
                    <select class="select" size="1" name="url_type" style="width:51%" >
                        <option value="0" >链接</option>
                        <option value="1" >商品ID</option>
                    </select>

                </div>
            </div>
            <div class="abc">
                <div class="abd">
                    商品链接或id：<input type="text" class="input-text radius" style="width:51%" name="url"></br>
                </div>
            </div>
            </br>
        <div class="abf">
            <div id="img">封面图：
                <input type="file" id="cover">
                <input type="hidden" name="cover" value="">
            </div>
        </div>
        <div class="abc">
            <div class="abd" style="margin-right: 0px"><input type="submit"  class="input-text radius" value="确定" style="width:51%" ></div>
            <div class="abe">
                <input type="button" class="input-text radius" value="取消" onclick="b()" style="width:51%" >
            </div>
        </div>--}}
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
                    $('#img').append(addimg);
                    $("[name= cover]").val(data.img);
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