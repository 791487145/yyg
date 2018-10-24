@extends('wx.layout')
@section('title')
    123
@endsection
@section('content')
    <div id="location">
        <form action="/wxLocation/pic" method="post" enctype="multipart/form-data">
            <input type="file" name="Filedata">
            <input type="submit">
        </form>
    </div>
@section("javascript")
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        //alert(location.href.split('#')[0]);
        /*wx.config({
            debug: true,
            appId: '<php echo $signPackage["appId"];?>',
            timestamp: '<php echo $signPackage["timestamp"];?>',
            nonceStr: '<php echo $signPackage["nonceStr"];?>',
            signature: '<php echo $signPackage["signature"];?>',
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
                    alert(latitude);


                },
                cancel: function (res) {
                    alert('用户拒绝授权获取地理位置');
                },

            });

        });*/




    </script>
@endsection

