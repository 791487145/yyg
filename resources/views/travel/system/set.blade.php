@extends('travel')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>个人设置</span></h2>
            <form class="form" method="post">
                <div class="box">
                    <h4>旅行社信息</h4>
                    <table>
                        <tr>
                            <th>旅行社名称：</th>
                            <td><input type="text" name="ta_name" value="{{isset($user->ta_name)?$user->ta_name:''}}"></td>
                        </tr>
                        <tr class="area">
                            <th>所在地：</th>
                            <td>
                                <select name="ta_province_id">
                                    <option value="0">请选择省份</option>
                                    @forelse($provices as $province)
                                        <option value="{{$province->id}}" @if($province->id == $user->ta_province_id) selected @endif>{{$province->name}}</option>
                                    @empty

                                    @endforelse
                                </select>
                                <select name="ta_city_id">
                                    @forelse($citys as $city)
                                        <option value="{{$city->id}}" @if($city->id == $user->ta_city_id) selected @endif>{{$city->name}}</option>
                                    @empty
                                        <option value="0">请选择城市</option>
                                    @endforelse
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>负责人手机号码：</th>
                            <td><input type="text" name="mobile" value="{{isset($user->mobile)?$user->mobile:''}}" disabled></td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>旅行社logo：</th>
                            <td>
                                <div class="fileResult oneFileResult">
                                    <span class="logo-img">
                                    @if(!empty($user->ta_logo))
                                        <img id="image_logo" src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$user->ta_logo}}" width="150" height="150">
                                    @endif
                                    </span>
                                    <input type="hidden" name="ta_logo" value="{{isset($user->ta_logo)?$user->ta_logo:''}}"/>
                                    <span>
                                        <label class="fileUpload">
                                        <input type="file" id="logo-upload" accept="image/jpeg,image/png,image/gif"/>
                                        </label>
                                    </span>
                                </div>

                                <p class="tip">（建议150*150像素）</p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="otherFormLink">
                    实名认证
                    @if($user->state == 0 || $user->state == 3)
                        <a href="{{url('system/authentication')}}">点击进入</a>
                        @else
                        <a href="{{url('system/authenticate')}}">点击进入</a>
                    @endif
                    @if($user->state == 1)
                        <span class="status">已认证</span>
                        @elseif($user->state == 2)
                        <span class="status">正在审核中</span>
                        @elseif($user->state == 3)
                        <span class="status">审核不通过，重新提交实名认证</span>
                        @endif

                </div>
                <div class="footButton">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                    <input type="submit" class="button" value="保存">
                    <a href="{{url('auth/logout')}}" class="but-no" style="width: 98px;">退出账号</a>
                </div>
            </form>
        </div>
    </div>
    </div>
    {{--<script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>--}}
    <script type="text/javascript" src="/wx/js/pict.js"></script>
    <script>
        $(function(){
            $(".form").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'yes') {
                        layer.alert(data.msg,function(){
                            location.reload();
                        });
                    } else {
                        layer.alert(data.msg, {icon:2,time:5000});
                    }
                }
            });

            $('[name=ta_province_id]').bind('change',function(){
                $.get('/system/getCity/'+$(this).val(),'',function (data) {
                    if(data.ret == 'yes'){
                        var option = '<option value="0">请选择城市</option>';
                        $.each(data.data,function(k,v){
                            option += '<option value="'+v.id+'">'+v.name+'</option>'
                        })
                        $('[name=ta_city_id]').html(option);

                    }
                });
            });
        });


        $(function(){
            var _upFile=document.getElementById("logo-upload");
            _upFile.addEventListener("change",function(){
                if (_upFile.files.length === 0) {
                    alert("请选择图片");
                    return; }
                var oFile = _upFile.files[0];
                if(!new RegExp("(jpg|jpeg|png)+","gi").test(oFile.type)){
                    alert("照片上传：文件类型必须是JPG、JPEG、PNG");
                    return;
                }
                var reader = new FileReader();
                reader.onload = function(e) {
                    var base64Img= e.target.result;
                    var _ir=ImageResizer({
                        resizeMode:"auto"
                        ,dataSource:base64Img
                        ,dataSourceType:"base64"
                        ,maxWidth:150 //允许的最大宽度
                        ,maxHeight:150 //允许的最大高度。
                        ,onTmpImgGenerate:function(img){
                        }
                        ,success:function(resizeImgBase64,canvas){
                            $.post('/system/uploads',{imgOne:resizeImgBase64},function(data){
                                var img = '<img id="image_logo" src="{{env('IMAGE_DISPLAY_DOMAIN')}}'+data+'" width="150" height="150">'
                                $('.logo-img').html(img);
                                $('[name=ta_logo]').val(data);
                            })
                        }
                        ,debug:true
                    });
                };
                reader.readAsDataURL(oFile);

            },false);
        });
    </script>
    @stop
