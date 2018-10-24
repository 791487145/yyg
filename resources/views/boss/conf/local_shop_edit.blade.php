@extends('layout_pop')
<style>
    .buttonBg{background: #e0e0e0;padding: 10px;}
    .buttonBg span
    {
        float: right;
        transform:rotate(1deg);
        -ms-transform:rotate(1deg); 	/* IE 9 */
        -moz-transform:rotate(1deg); 	/* Firefox */
        -webkit-transform:rotate(1deg); /* Safari 和 Chrome */
        -o-transform:rotate(1deg); 	/* Opera */
    }

    .buttonBg span.cua
    {
        transform:rotate(90deg);
        -ms-transform:rotate(90deg); 	/* IE 9 */
        -moz-transform:rotate(90deg); 	/* Firefox */
        -webkit-transform:rotate(90deg); /* Safari 和 Chrome */
        -o-transform:rotate(90deg); 	/* Opera */

    }
    .checkDiv .m20{margin-right:20px; line-height: 40px;width: 150px;display: inline-block;}
</style>
@section("content")
    <link rel="stylesheet" type="text/css" href="{{ asset('lib/uploadify/uploadify.css') }}">
    <form action="/conf/confShop/update" method="post" id="form">
    <div>
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td>
                    所属分馆：<input type="text" datatype="*1-6" class="input-text radius" style="width:40%" name="name" value="{{$ConfPavilions->name}}">
                    <input type="hidden"  name="id" value="{{$ConfPavilions->id}}">
                </td>
                <td>排序：<input type="text" class="input-text radius" style="width:30%" name="display_order" value="{{$ConfPavilions->display_order}}"></td>
            </tr>
            <tr>
                <td colspan="2" id="img">
                    <input type="file" id="cover" name="cover">
                    <input type="hidden" name="cover" value="{{$ConfPavilions->cover}}">
                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfPavilions->cover}}" alt="" class="radius" width="50px" height="40px">;
                </td>
            </tr>

            <tr>
                <td colspan="2" id="newImgs">
                    <input type="file" id="newImg" name="newImg">
                    <input type="hidden" name="newImg" value="{{$ConfPavilions->new_cover}}">
                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfPavilions->new_cover}}" alt="" class="radius" width="50px" height="40px">;
                </td>
            </tr>
            <tr>
                <td colspan="2" id="imgs">
                    <input type="file" id="background" name="background">
                    <input type="hidden" name="background" value="{{$ConfPavilions->background}}">
                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfPavilions->background}}" alt="" class="radius" width="50px" height="40px">;
                </td>
            </tr>
            <tr>
                <td colspan="2" id="description">
                    地方馆备注:
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="leftText" >
                        <div class="pos-r">
                            <textarea id="txt" name="description" cols="20%" rows="10%" datatype="*" class="textarea radius">{{$ConfPavilions->description}}</textarea>
                            <div class="pos-a" style="bottom: -10px;right: 5px;"><span id="word">0</span>/120</div>
                        </div>
                        <script language="javascript" type="text/javascript">
                            $("#txt").keyup(function(){
                                var lengths = $("#txt").val().length;
                                var content = $("#txt").val();
                                if(lengths > 120){
                                    $("#txt").val( content.substring(0,120) );
                                    $("#word").text("120") ;
                                }else {
                                    $("#word").text(lengths) ;
                                }

                            });
                        </script>
                    </div>
                </td>
            </tr>
        </table>
        <div class="buttonBg">选择辐射区域<span> >> </span></div>
        <div class="ml-10 mr-10 checkDiv">
            @foreach($citys as $city)

                <span class="m20">

                    @if(in_array($city->id,$selectedCitys))
                        <input type="checkbox" name="citys[]" disabled value="{{$city->id}}"/><span style="color: #999;margin-left: 4px;">{{$city->name}}</span>
                    @elseif(in_array($city->id,$selfSelectedCitys))
                        <input type="checkbox" name="citys[]" checked value="{{$city->id}}"/><span style="color: #000;margin-left: 4px;">{{$city->name}}</span>
                    @else
                        <input type="checkbox" name="citys[]" value="{{$city->id}}"/><span style="color: #000;margin-left: 4px;">{{$city->name}}</span>
                    @endif
            </span>

            @endforeach

        </div>
        <div class="ml-10">添加标签</div>
        <table class="table table-border table-bordered table-hover">
            <thead>
                
            </thead>
            <tbody id="abc">
            @foreach($ConfPavilionTags as  $ConfPavilionTag)
            <tr id="tr_{{$ConfPavilionTag->id}}">
                <td>标签名<input type="text" class="input-text radius" style="width:30%" name="tag_name[]" value="{{$ConfPavilionTag->name}}"><span class="c-red ml-10">不超过六个字</span></td>
                <td>商品ID<input type="text" class="input-text radius" style="width:30%" name="goods_id[]" value="{{$ConfPavilionTag->goods_id}}"><input type="hidden"  name="tag_id[]" value="{{$ConfPavilionTag->id}}"></td>
                <td>排序<input type="text" class="input-text radius" style="width:30%" name="display_order_tag[]" value="{{$ConfPavilionTag->display_order}}"></td>
                <td>
                    <a title="删除" href="javascript:;" onclick="confirm_action('确定需要删除该标签吗？','/conf/confShop/del/delTag/{{$ConfPavilionTag->id}}')"  class="ml-5 delete" style="text-decoration:none">
                        <i class="Hui-iconfont">&#xe60b;</i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </br>
        <div class="ml-10 addTable">
            <input type="button" onclick="add()"  class="btn btn-success radius" value="添加" style="width:10%" ><span class="c-red ml-10">不超过八个</span>
        </div>
        </br>
        <div style="margin: 100px 200px 0;">
        	<input type="button" onclick="b()" class="btn btn-default  radius f-l" value="取消" style="width:40%;">
        	<input type="submit"  class="radius btn btn-primary f-r" value="确定" style="width:40%;">
        </div>
    </div>
    </form>
@endsection
@section("javascript")

    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
    <script>
        $(".buttonBg").click(function(){
            var cua = $(".buttonBg").find(".cua").text();
            if(cua){
                $(".buttonBg span").removeClass("cua");
            }else {
                $(".buttonBg span").addClass("cua");
            }
            $(".checkDiv").toggle();
        })

        <?php $timestamp = time();?>
        $(function(){
            $('#cover').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/conf/upload') }}',
                'buttonText': '封面图',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png; *.jpeg',
                'height': 30,
                'width': 150,
                'multi':false,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    $('#img>img').remove();
                    var addimg = '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}' + data.img + '" alt="" class="radius" width="50px" height="40px">';
                    $('#img').append(addimg);
                    $("[name= cover]").val(data.img);
                }
            });

            $('#newImg').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/conf/upload') }}',
                'buttonText': '新封面图',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png; *.jpeg',
                'height': 30,
                'width': 150,
                'multi':false,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    if($("#newImage").length > 0){
                        $('#newImage').attr('src',"{{env("IMAGE_DISPLAY_DOMAIN")}}" + data.img);
                    }else{
                        var addimg = '<img id="newImage" src="{{env("IMAGE_DISPLAY_DOMAIN")}}' + data.img + '" alt="" class="radius" width="50px" height="40px"">';
                        $('#newImgs').append(addimg);
                    }

                    $("[name= newImg]").val(data.img);
                }
            });

            $('#background').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/conf/upload') }}',
                'buttonText': '背景图',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png; *.jpeg',
                'height': 30,
                'width': 150,
                'multi':false,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    $('#imgs>img').remove();
                    var addimg = '<img src="{{env("IMAGE_DISPLAY_DOMAIN")}}' + data.img + '" alt="" class="radius" width="50px" height="40px"">';
                    $('#imgs').append(addimg);
                    $("[name= background]").val(data.img);
                }
            });
        })

    </script>
    <script>

        function b(){
            layer_close();
        }

        function confirm_action(message,url){
            layer.confirm(message,function(index){
                $.get(url,function (data) {
                    $("#tr_"+data).remove();
                    layer.msg('已删除!',{icon:1,time:1000});
                })
            });
        }

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

        function add(){
            var num = $('#abc').children('tr').length;
            num = num*1+1;
            if(num < 9){
                var tr = "<tr>" +
                        "<td>标签名<input type='text' class='input-text radius' style='width:30%' name='tag_names[]'><span class='c-red'>不超过六个字</span></td>" +
                        "<td>商品ID<input type='text' class='input-text radius' style='width:30%' name='goods_ids[]'></td>" +
                        "<td>排序<input type='text' class='input-text radius' value='' style='width:30%' name='display_order_tags[]'></td>" +
                        "<td>" +
                        "<a title='删除' href='javascript:;'  class='ml-5 del' style='text-decoration:none'>" +
                        "<i class='Hui-iconfont'>&#xe60b;</i>" +
                        "</a>" +
                        "</td>" +
                        "</tr>";
                $("#abc").append(tr);
                $('.del').click(function(){
                    $(this).parent().parent().remove();
                    $(".addTable").show();
                })
            }else{
                $(".addTable").hide();
            }
        }


    </script>
@endsection