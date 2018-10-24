@extends('wx.layout')
@section('title')
    个人设置
@endsection
@section('content')
<div class="content setWrap">
    <ul class="mineLink">
        <li class="lineB"><a href="" class="linkArrow">更换头像<span class="right"><label class="photo"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{env('DEFAULT_AVATAR')}}"></label></span></a></li>
        <li class="lineB"><label class="linkArrow">用户昵称<input id="username" type="text" value="<?php echo $username['nick_name']?>" class="right"><input id="z-hidden" type="hidden" value="<?php echo $username['id']?>"></label></li>
        <li class="lineB"><a href="http://www.yyougo.com/app/about.html" class="linkArrow">关于易游购<span class="arrow"></span></a></li>
    </ul>
</div>
@endsection
@section('bottom_bar')
    @include('wx.bottom_bar')
@endsection
@section('javascript')
    <script type="text/javascript">
        $("#username").blur(function(){
            var id = $("#z-hidden").val();
            var name = $("#username").val();
           $.ajax({
                type:'get',
                url:'/save/',
                dataType:'json',
                data:{'nick_name':name},
                success:function(data){

                }
            });
        })

    </script>
@endsection