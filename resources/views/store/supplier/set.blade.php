@extends('supplier')
@section('content')
    <style>
        #send{font-size:14px;width: 110px;}
        .uploadify-queue{display: none;}
        .tab-del{display: block;width: 19px;height: 19px;position: relative;left: 810px;background: url("{{asset('images/icon_del.png')}}");cursor: pointer; }
        .img-del{float: left;background: url("{{asset('images/acrossTab-close.png')}}") -91px -13px;cursor: pointer;width: 13px;height: 13px;display: inline-block;position: relative;left: -12px;}
        .goodsTable{width:800px !important;}
        .limitText{width: 250px;}
    </style>
<div class="rightCon">
    <div class="wrap">
        <h2><span>个人设置</span></h2>
        <form class="form" method="post" action="{{url('supplier/setting')}}">
            <div class="box">
                <h4>供应商信息</h4>
                <table>
                    <tr>
                        <th>真实姓名：</th>
                        <td><input type="text" name="name" value="{{$supplier->name}}" disabled /></td>
                    </tr>
                    <tr>
                        <th>头像：</th>
                        <td>
                            <div class="fileResult oneFileResult">
                                <span class="avatar-img">
                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$supplier->avatar}}" width="@if($supplier->avatar) 100 @else 0 @endif" height="100"></span>
                                    <input type="hidden" name="avatar" value="{{$supplier->avatar}}"/>
                                <span>
                                <label class="fileUpload">
                                    <input type="file" id="avatar-upload" accept="image/jpeg,image/png,image/gif"/>
                                </label>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr class="area">
                        <th>所在地：</th>
                        <td>
                            <select name="province_id" class="input-text supplier-select">
                                <option value="0">选择省份</option>
                                @foreach($province as $item)
                                    <option value="{{$item->id}}" @if($item->id == $supplier->province_id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                            <select name="city_id" class="input-text supplier-select">
                                <option value="0">选择市</option>
                                @foreach($city as $val)
                                    <option value="{{$val->id}}" @if($val->id == $supplier->city_id) selected @endif>{{$val->name}}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>手机号码：</th>
                        <td><input type="text" name="mobile" value="{{$supplier->mobile}}" disabled></td>
                    </tr>
                </table>
            </div>
            <div class="box">
                <h4>店铺信息</h4>
                <table>
                    <tr>
                        <th>店铺名称：</th>
                        <td><input type="text" name="store_name" value="{{$supplier->store_name}}"></td>
                    </tr>
                    <tr>
                        <th>店铺logo：</th>
                        <td>
                            <div class="fileResult oneFileResult">
                                <span class="logo-img">
                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$supplier->store_logo}}" width="@if($supplier->store_logo) 100 @else 0 @endif" height="100"></span>
                                    <input type="hidden" name="store_logo" value="{{$supplier->store_logo}}"/>
                                <span>
                                <label class="fileUpload">
                                    <input type="file" id="logo-upload" accept="image/jpeg,image/png,image/gif"/>
                                </label>
                                </span>
                            </div>
                        </td>

                    </tr>
                    <tr class="area">
                        <th>商品发货地：</th>
                        <td>
                            <select name="store_province_id" class="input-text supplier-select">
                                <option value="0">选择省份</option>
                                @foreach($province as $item)
                                    <option value="{{$item->id}}" @if($item->id == $supplier->store_province_id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                            <select name="store_city_id" class="input-text supplier-select">
                                <option value="0">选择市</option>
                                @foreach($store_city as $val)
                                    <option value="{{$val->id}}" @if($val->id == $supplier->store_city_id) selected @endif>{{$val->name}}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footButton">
                <input type="submit" value="保存">
                <a href="{{url('auth/logout')}}" class="headR">退出账号</a>
            </div>
        </form>
    </div>
</div>
</div>

    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        <?php $timestamp = time();?>
        $(function () {
            //获取市级
            $('[name=province_id]').bind('change',function(){
                var province = $(this).val();
                var html = '<option value="0">选择市</option>';
                $.get('/supplier/getCity/'+province,function(json){
                    $.each(json,function(k,v){
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    $('[name=city_id]').html(html);
                });
            });
            $('[name=store_province_id]').bind('change',function(){
                var province = $(this).val();
                var html = '<option value="0">选择市</option>';
                $.get('/supplier/getCity/'+province,function(json){
                    $.each(json,function(k,v){
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    $('[name=store_city_id]').html(html);
                });
            });
            //店铺头像上传
            $('#avatar-upload').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/supplier/upload') }}',
                'buttonText': '',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 100,
                'width': 100,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    $('.avatar-img img').attr('src','{{env('IMAGE_DISPLAY_DOMAIN')}}'+data.img);
                    $('.avatar-img img').attr("width","100");
                    $('[name=avatar]').val(data.img);
                }
            });
            //店铺头LOGO上传
            $('#logo-upload').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/supplier/upload') }}',
                'buttonText': '',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.gif; *.jpg; *.png',
                'height': 100,
                'width': 100,
                'onUploadSuccess': function (file, data, response) {
                    data = JSON.parse(data);
                    $('.logo-img img').attr('src','{{env('IMAGE_DISPLAY_DOMAIN')}}'+data.img);
                    $('.logo-img img').attr("width","100");
                    $('[name=store_logo]').val(data.img);
                }
            });
        });

    </script>
    @stop