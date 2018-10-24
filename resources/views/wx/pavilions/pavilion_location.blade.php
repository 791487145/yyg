@extends('wx.layout')
<?php
/*require_once(app_path().'/Lib/Wx/jssdk.php');
$jssdk = new JSSDK(env("WX_APPID"), env("WX_APPSECRET"));
$signPackage = $jssdk->GetSignPackage();
*/?>
@section('title')
    {{$ConfPavilion->name}}
@endsection
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">地方馆</div>
</div>
<div class="yyg-content">
	<span>
		<span class="yyg-addressIcon1 yyg-color" id="place"></span>
	</span>
	<span style="float: right;">
		<span class="yyg-color6" id="absolute">
			<span class="yyg-LocationIcon fl" id="LocationIcon"></span>
			<span style="line-height:28px;margin-left: 5px;">重新定位</span>
		</span>
	</span>
</div>
<div class="yyg-title">当前所属馆</div>
<div class="branchList" style="padding-bottom: 20px;">
    <h3><span>{{$ConfPavilion->name}}</span></h3>
    <a href="/pavilions/{{$ConfPavilion->id}}"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfPavilion->new_cover}}?imageslim"></a>
</div>
<div class="yyg-title">其他地方馆</div>
<div class="branchList" style="padding-bottom: 20px;">
    @foreach($Pavilions as $Pavilion)
    <h3><span>{{$Pavilion->name}}</span></h3>
    <a href="/pavilions/{{$Pavilion->id}}"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$Pavilion->new_cover}}?imageslim"></a>
    @endforeach
</div>
<input type="hidden" value="0" class="pavilion_id">
@endsection
@section("javascript")
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: '<?php echo $signPackage["timestamp"];?>',
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'checkJsApi',
            'openLocation',
            'getLocation'
        ]
    });

    wx.ready(function(){
            wx.getLocation({
                success: function (res) {
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    var speed = res.speed; // 速度，以米/每秒计
                    var accuracy = res.accuracy; // 位置精度
                    $.post('/city',{lat:latitude,lon:longitude},function(msg){
                        $("#place").html("当前位置："+msg.province);
                        $("#absolute").click(function(){
                        	$("#LocationIcon").addClass("rotateIcon");
                            if(msg.ret == "no"){
                                var conten = "系统定位到您在"+msg.province+"，该地方未开通地方馆，已为您切换至乡亲直供馆！";
                            }
                            if(msg.ret == "yes"){
                                var conten = "系统定位到您在"+msg.province+"，需要切换至"+msg.content.name+"吗？";
                                $(".pavilion_id").val( msg.content.id);
                            }
                            var title = "提示";
                            $("#LocationIcon").removeClass("rotateIcon");
                            alertPopup({"title":title,"conten":conten},function(){
                                var pavilionID = $(".pavilion_id").val();
                                location.href='/pavilions/'+pavilionID+'abc';
                            });
                        })
                    })
                },

                cancel: function (res) {
                    $("#place").html("未打开定位权限");
                    var conten = "系统检测到您的定位权限未打开，暂定位不到所属地方馆，已默认为您切换至乡亲直供馆！";
                    alertPopup({"title":"提示","conten":conten},function(){
                        var pavilionID = $(".pavilion_id").val();
                        location.href='/pavilions/'+pavilionID+'abc';
                    });
                },
            });

    });
</script>
@endsection