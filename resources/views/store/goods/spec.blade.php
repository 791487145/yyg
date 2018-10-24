@extends('layout_pop')
@section("content")
    <style>
        /*文件上传样式重写*/

        .uploadify {
            position: relative;
            left: 50px;
            margin-top: 10px;
            border: 1px solid #c0c0c0;
        }

        .uploadify-queue {
            display: none;
        }

        .uploadify-button-text {
            line-height: 45px;
            font-size: 50px;
            text-align: center;
            display: inline-block;
            width: 50px;
            height: 50px;
            color: #c0c0c0;
        }
        .gift-label{
            width: 50px;
            display: inline-block;
            text-align: right;
        }
        .btn-handle{margin:40px 150px !important; }
        .btn-handle .btn{width: 140px;text-align: center;margin-right: 50px;}
    </style>
    <div class="pd-20">
        <form action="{{url('spec/add')}}" method="post" class="form form-horizontal" id="gift-add">
            <div class="row">
                <label class="gift-label">图片：</label>
                <img src="" class="spec-img" width="120" height="120">
                <input type="hidden" name="image" />

                <input type="file" id="des-upload"/>
            </div>
            <div class="row">
                <label class="gift-label">介绍：</label>
                <textarea name="description"  cols="80" rows="10"></textarea>
            </div>


            <div class="row btn-handle">

                <button type="submit" class="btn btn-success radius" ><i class="icon-ok"></i>确定</button>
                <button type="button" class="btn btn-danger radius" onclick="layer_close();">取消</button>


            </div>
        </form>
    </div>
    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
@endsection

@section("javascript")

    <script>

        $(function(){
            $("#gift-add").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(res){
                    if(res.ret == 'yes') {
                        layer.alert(res.msg,{icon:1,time:1000});
                        var content = '<div class="col-md-10 mt-10"><div class="row des-upload">' +
                            '<img src="http://{{env('IMAGE_DOMAIN')}}/'+res.data.image+'" class="des-img" width="200" height="200"/>' +
                            '<span class="desc-del"></span><input type="hidden" name="desc_name[]" value="'+res.data.image+'" /><div class="row">' +
                            '<textarea name="desc_description[]" class="mt-10" cols="70" rows="10">'+res.data.description+'</textarea></div><span class="desc-del-test"></span>' +
                            '</div></div>';
                        parent.$('.desc-content').append(content);
                        parent.desc_remove();
                        layer_close();
                        //parent.location.replace(parent.location.href);
                    } else {
                        layer.alert(res.msg, {icon:2,time:5000});
                    }
                }
            });
            //商品详情
            <?php $timestamp = time();?>
            $('#des-upload').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/goods/upload') }}',
                'buttonText': '+',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 50,
                'width': 50,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    $('.spec-img').attr('src', '{{env("IMAGE_DISPLAY_DOMAIN")}}' + data.img);
                    $('[name=image]').val(data.img);
                }
            });
        });
    </script>
@endsection