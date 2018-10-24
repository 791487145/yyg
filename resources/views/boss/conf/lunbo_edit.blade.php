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
        <form id="form" action="/conf/confLunBo/edit" method="post">
            <table class="table table-border table-bordered table-hover">
                <tr>
                    <td>所属应用:
                        <span class="select-box" style="width: 51%">
                            <select class="select" size="1" name="location" >
                                <option value="0" @if($confBanners->location == 0) selected @endif>微商城</option>
                                <option value="1" @if($confBanners->location == 1) selected @endif>app导游端</option>
                            </select>
                        </span>
                    </td>
                    <td>所属分馆:
                        <span class="select-box" style="width: 51%">
                            <select class="select" size="1" name="pavilion_id" >
                                @foreach($confPavilions as $confPavilion)
                                    @if($confBanners->pavilion_id == $confPavilion->id)
                                        <option selected value="{{$confPavilion->id}}" >{{$confPavilion->name}}</option>
                                    @else
                                        <option value="{{$confPavilion->id}}" >{{$confPavilion->name}}</option>
                                    @endif
                                @endforeach
                                    <option value="9999" @if($confBanners->pavilion_id == 9999) selected @endif >地方馆</option>
                            </select>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        轮播图名：<input type="text" datatype="*" class="input-text radius" style="width:51%" name="name" value="{{$confBanners->name}}">
                    </td>
                    <td>
                        商品链接类型：
                        <select class="select" size="1" name="url_type" style="width:51%" >
                            @if($confBanners->url_type == 0)
                                <option value="0" selected >链接</option>
                                <option value="1" >商品ID</option>
                            @else
                                <option value="0" >链接</option>
                                <option value="1" selected>商品ID</option>
                            @endif
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        商品链接或id：<input type="text" class="input-text radius" style="width:51%" name="url_content" value="{{$confBanners->url_content}}">
                    </td>
                    <td>
                        排序：<input type="text" class="input-text radius" datatype="n" style="width:51%" name="display_order" value="{{$confBanners->display_order}}">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="img">封面图：
                            <input type="file" id="cover">
                            <input type="hidden" name="id" value="{{$confBanners['id']}}">
                            <input type="hidden" name="cover" value="{{$confBanners['cover']}}">
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$confBanners['cover']}}" alt="" width="50%" height="50%">;
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>开始时间
                        <input type="text" class="Wdate" style="width:51%" name="start_time" id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="{{$confBanners->start_time}}"/>
                    </td>
                    <td>结束时间：
                        <input type="text" class="Wdate" style="width:60%" name="end_time" id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="{{$confBanners->end_time}}"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit"  class="btn btn-success radius" value="确定" style="width:30%" >
                    </td>
                    <td>
                        <input type="button" class="btn btn-default radius f-l" value="取消" onclick="b()" style="width:30%" >
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