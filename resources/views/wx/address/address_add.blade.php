@extends('wx.layout')
@section('title')
新增地址
@endsection
<link rel="stylesheet" href="wx/css/iosSelect.css">
@section('content')
<style>
	ul,li,input,span{font-size: 14px;}
</style>
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">新增地址</div>
</div>
<div class="content addressEdit">
    <ul>
        <li class="lineB">您的手机号:<label><input type="hidden" value="{{$mobile[0]}}" name="tel"><input class="phone" name="phone" type="number" onkeyup="this.value=this.value.replace(/^[0]\d*$/,'')" placeholder="请输入您本人手机号码" value="{{$mobile[0]}}"}}></label></li>
        <li class="lineB">收&nbsp;&nbsp;&nbsp;货&nbsp;&nbsp;&nbsp;人:<label><input name="name" type="text" placeholder="请输入收货人姓名"></label></li>
        <li class="lineB">收货人电话:<label><input name="mobile" class="mobile" onkeyup="this.value=this.value.replace(/^[0]\d*$/,'')" type="number" placeholder="请输入收货人联系方式"/></label></li>
        <li class="lineB">所&nbsp;在&nbsp;地&nbsp;区:<label>
            <div class="form-item item-line" id="select_contact">               
                <div class="pc-box">                     
                    <input type="hidden" name="contact_province_code" data-id="0001" id="contact_province_code" value="" data-province-name="">
                    <input type="hidden" name="contact_city_code" id="contact_city_code" value="" data-city-name="">
                    <input type="hidden" name="contact_district_code" id="contact_district_code" value="" data-district-name="">
                    <span style="padding-left: 15px;" data-city-code="" data-province-code="" data-district-code="" id="show_contact"><i>请选择收货地所在区域</i></span>
                </div>             
            </div>
        </label></li>
        <li class="lineB">详&nbsp;细&nbsp;地&nbsp;址：<label><input type="text" name="address" placeholder="请输入收货地详细地址"></label></li>
    </ul>
    <div class="default"><input type="checkbox" name="is_default" id="defultCheck" value="1"><label for="defultCheck">设为默认收货地址</label></div>
</div>
<div class="footButton">
    <input type="button" class="button" value="保存" onclick="addr()">
</div>

@endsection
@section('javascript')
    <script>

        $(".phone,.mobile").keyup(function(){
            if($(this).val().length > 11){
                $(this).val( $(this).val().substring(0,11) );
            }
        });
        function addr(){
        	var phone = $("input[name = 'phone']").val();
            var mobile = $("input[name = 'mobile']").val();
            if(!(/^1[34578]\d{9}$/.test(phone))){
            	information("手机号码格式不正确");
                return false;
            }
            if(!(/^1[34578]\d{9}$/.test(mobile))){
            	information("收货人电话号码格式不正确");
                return false;
            }
            var tel = $("input[name = 'tel']").val();
            var city = $("#show_contact").attr('data-city-code');
            var province = $("#show_contact").attr('data-province-code');
            var district = $("#show_contact").attr('data-district-code');
            var phone = $("input[name = 'phone']").val();
            var name = $("input[name = 'name']").val();
            var mobile = $("input[name = 'mobile']").val();
            var address = $("input[name = 'address']").val();
            var is_default = $('input[name="is_default"]:checked').val();
            $.post('/address',{province:province,city:city,district:district,tel:tel,name:name,mobile:mobile,phone:phone,address:address,is_default:is_default},function(msg){
                if(msg.ret == 'yes'){
                    location.href = '/addresses';
                }
                if(msg.ret == 'not'){
                    var info = '请填写完整基本信息';
                    information(info);
                }
                if(msg.ret == 'no'){
                    //var info = '请填写手机号';
                    information(msg.msg);
                }
            })

        }
    </script>
@endsection


