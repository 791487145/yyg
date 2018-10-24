@extends('wx.layout')
@section('title')
    编辑地址
@endsection
<link rel="stylesheet" href="/wx/css/iosSelect.css">
@section('content')
<style>
	ul,li,input,span{font-size: 14px;}
</style>
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">编辑地址</div>
</div>
<div class="content addressEdit">
    <ul>
        <li class="lineB">收&nbsp;&nbsp;&nbsp;&nbsp;货&nbsp;&nbsp;&nbsp;人：<label><input type="text" name="name" placeholder="请输入收货人姓名" value="{{$UserAddress->name}}"></label></li>
        <li class="lineB">收货人电话：<label><input class="mobile" type="number" onkeyup="this.value=this.value.replace(/^[0]\d*$/,'')" name="mobile" placeholder="请输入收货人联系方式" value="{{$UserAddress->mobile}}"></label></li>
        <li class="lineB">所&nbsp;在&nbsp;地&nbsp;区：<label>
            <div class="form-item item-line" id="select_contact">
                <div class="pc-box">
                    <input type="hidden" name="contact_province_code" data-id="0001" id="contact_province_code" value="" data-province-name="">
                    <input type="hidden" name="contact_city_code" id="contact_city_code" value="" data-city-name="">
                    <span style="padding-left: 15px;" data-city-code="{{$UserAddress->city_id}}" data-province-code="{{$UserAddress->province_id}}" data-district-code="{{$UserAddress->district_id}}" id="show_contact">{{$UserAddress->province}}{{$UserAddress->city}}{{$UserAddress->district}}</span>
                </div>
            </div>
        </label></li>
        <li class="lineB">详&nbsp;细&nbsp;地&nbsp;址：<label><input type="text" name="address" placeholder="请输入收货地详细地址" value="{{$UserAddress->address}}"></label></li>
    </ul>
    <div class="default"><input type="radio" name="is_default" value="1" id="defultCheck" {{($UserAddress->is_default == 1) ? 'checked' : ''}}><label for="defultCheck">设为默认收货地址</label></div>
   {{-- <div class="contactPerson"><a href="" class="lineL">选联系人</a></div>--}}
</div>
</div>
<div class="footButton">
    <input type="button" class="button" value="保存" onclick="edit({{$UserAddress->id}})">
</div>

@endsection
@section('javascript')
    <script>
        $(".mobile").keyup(function(){
            if($(this).val().length > 11){
                $(this).val( $(this).val().substring(0,11) );
            }
        });

        function telephone(){
            var mobile = $("input[name = 'mobile']").val();
            if(!(/^1[34578]\d{9}$/.test(mobile))){
                return false;
            }
            return true;
        }

        function edit(id){
            if(!telephone()){
                var info = '手机号码格式不正确';
                information(info);
                return false;
            }
            var city_id = $("#show_contact").attr('data-city-code');
            var province_id = $("#show_contact").attr('data-province-code');
            var district_id = $("#show_contact").attr('data-district-code');
            var name = $("input[name = 'name']").val();
            var mobile = $("input[name = 'mobile']").val();
            var address = $("input[name = 'address']").val();
            var is_default = $('input[name="is_default"]:checked').val();
            $.post('/addresses',{province_id:province_id,city_id:city_id,district_id:district_id,name:name,mobile:mobile,id:id,address:address,is_default:is_default},function(msg){
                if(msg.ret == 'yes'){
                    location.href = '/addresses';
                }
            })

        }
    </script>
@endsection
