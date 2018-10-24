@extends('travel')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>个人设置</span><a href="{{url('auth/logout')}}" class="headR">退出</a></h2>
            <form class="form" id="authentication" method="post" action="{{url('system/authentication')}}">
                <div class="box">
                    <h4>实名认证</h4>
                    <table>
                        <tr>
                            <th>真实姓名：</th>
                            <td width="400"><input type="text" name="opt_name" datatype="s2-16" nullmsg="请输入真实姓名" errormsg="请输入真实姓名" maxlength="16" ></td>
                            <th>身份证号码：</th>
                            <td width="400"><input type="text" name="opt_id_card" datatype="n18-18" nullmsg="请输入身份证号码" errormsg="请输入身份证号码" maxlength="18" >
                                {{--<span class="pass"></span>--}}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>上传正面手持身份证：</th>
                            <td>
                                <div class="fileResult oneFileResult">
                                    <span class="opt-photo-1"><img src="" width="150" height="150"><input type="hidden" name="opt_photo_1"></span>
                                    <span>
                                        <label class="fileUpload">
                                            <input type="file" id="file-x"/>
                                        </label>
                                    </span>
                                </div>
                                {{--<span class="pass"></span>--}}
                            </td>
                        </tr>
                        <tr>
                            <th>上传反面手持身份证：</th>
                            <td>
                                <div class="fileResult oneFileResult">
                                    <span class="opt-photo-2"><img src="" width="150" height="150"><input type="hidden" name="opt_photo_2"></span>
                                    <span>
                                        <label class="fileUpload">
                                            <input type="file" id="file-y"/>
                                        </label>
                                    </span>
                                </div>
                                {{--<span class="pass"></span>--}}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="footButton">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                    <input type="submit" value="提交审核">
                </div>
            </form>
        </div>
    </div>
    </div>
    <div class="popupBg"></div>
    <div class="popupWrap confirmWrap applySuccess">
        <div class="title"><b>实名认证</b></div>
        <div>
            <p class="textC">恭喜你的实名认证成功提交~</p>
            <p>我们的工作人员会在1-2个工作日内尽快审核，请耐心等待......</p>
            <div class="buttonGroup">
                <input type="button" class="button close" value="确定">
            </div>
        </div>
    </div>
    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
    <script>
        $(function(){
            <?php $timestamp = time();?>
            $('#file-x').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/system/upload') }}',
                'buttonText': '',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 150,
                'width': 150,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    if (data.ret == 'yes'){
                        $('.opt-photo-1 img').attr('src','{{env('IMAGE_DISPLAY_DOMAIN')}}'+data.msg);
                        $('[name=opt_photo_1]').val(data.msg);
                    }else{
                        layer.alert(data.msg, {icon:2,time:5000});
                    }
                }
            });

            $('#file-y').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/system/upload') }}',
                'buttonText': '',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 150,
                'width': 150,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    if (data.ret == 'yes'){
                        $('.opt-photo-2 img').attr('src','{{env('IMAGE_DISPLAY_DOMAIN')}}'+data.msg);
                        $('[name=opt_photo_2]').val(data.msg);
                    }else{
                        layer.alert(data.msg, {icon:2,time:5000});
                    }
                }
            });

            $("#authentication").Validform({
                tiptype:3,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'yes') {
                        popupConfirm('applySuccess');
                    } else {
                        layer.alert(data.msg, {icon:2,time:5000});
                    }
                }
            });
            $(".popupBg,.popupWrap .buttonGroup .close").click(function(){
                location.href='{{url('system/set')}}';
            });
        });
    </script>
    @stop
