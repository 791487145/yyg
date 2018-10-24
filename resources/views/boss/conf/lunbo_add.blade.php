@extends('layout_pop')
    <style>
        #all{
            margin-left: 1%;
        }
        .abc{
            width: 100%;
            height: 12%;
        }
        .abc>div {
            float: left;
        }
        .abd{
            width: 40%;
        }
        .abe{
            margin-left: 10%;
            width: 30%;
        }
        .abf{
            height: 30%;
        }
    </style>
@section("content")
    <link rel="stylesheet" type="text/css" href="{{ asset('lib/uploadify/uploadify.css') }}">
    <div id="all">
        <form id="form" action="/conf/confLunBo/add" method="post">
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
                                @foreach($confPavilions as $confPavilion)
                                    <option value="{{$confPavilion->id}}" >{{$confPavilion->name}}</option>
                                @endforeach
                                    <option value="9999" >地方馆</option>
                            </select>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        轮播图名：<input type="text" datatype="*" class="input-text radius" style="width:51%" name="name">
                    </td>
                    <td>
                        商品链接类型：
                        <select class="select" size="1" name="url_type" style="width:51%" >
                            <option value="0" >商品链接</option>
                            <option value="1" >商品ID</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        商品链接或id：<input type="text" class="input-text radius" datatype="*" style="width:51%" name="url_content">
                    </td>
                    <td>
                        排序：<input type="text" class="input-text radius" datatype="n" style="width:51%" name="display_order">
                    </td>
                </tr>
            </table>
        <div class="abf">
            <div id="img">封面图：<span style="color: red">首页上方轮播图尺寸：750*400px</span>
                <input type="file" id="cover">
                <input type="hidden" name="cover" value="">
            </div>
        </div>
        <div class="abc">
            <div class="abd">开始时间：
                <input type="text" class="Wdate" style="width:51%" name="start_time" id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="0000-00-00 00:00:00"/>
            </div>
            <div class="abe">
                结束时间：<input type="text" class="Wdate" style="width:60%" name="end_time" id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="0000-00-00 00:00:00"/>
            </div>
        </div>
        <div  style="margin:50px 200px">
        	<input type="submit"  class="btn btn-success radius f-l" value="确定" style="width:30%" >
            <input type="button" class="btn btn-default radius f-r" value="取消" onclick="b()" style="width:30%" >
        </div>
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
                    if($("img").length > 0){
                        $('img').attr('src',"{{env("IMAGE_DISPLAY_DOMAIN")}}" + data.img);
                    }else{
                        var addimg = '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}' + data.img + '" alt="" width="50%" height="45%">';
                        $('#img').append(addimg);
                    }


                    $("[name= cover]").val(data.img);
                }
            });
        })
    </script>
    <script>
        $(function(){
            $("#form").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    parent.location.replace(parent.location.href);
                }
            });
        })

        function b(){
            layer_close();
        }

    </script>
@endsection