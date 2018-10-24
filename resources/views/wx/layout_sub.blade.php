<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
    <title>@yield("title")</title>
    <link rel="stylesheet" type="text/css" href="/wx/css/swiper.css">
    <link rel="stylesheet" href="/wx/css/wx.css?v=<?php echo date('Ymdhi');?>">
</head>

<body>
@yield("content")
<div class="winTip addCartTip"><span id="add"></span></div>
<script type="text/javascript" src="/wx/js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="/wx/js/main.js?v=<?php echo date('Ymd');?>"></script>
<script type="text/javascript" src="/wx/js/swiper-3.4.0.jquery.min.js"></script>
<script src="/wx/js/iscroll.js"></script>
<script src="/wx/js/city.js"></script>
<script src="/wx/js/iosSelect.js"></script>
<script src="/wx/js/area.js"></script>
<script type="text/javascript">
    var swiper = new Swiper('.swiper-container',{
        loop: true,
        pagination: '.swiper-container .pagination',
        paginationClickable: true,
        autoplay: 2500,
        autoplayDisableOnInteraction: false
    })
    $(function(){
        var num = $(".footNav .shopCartButton b").text();
        if(num>0) {$(".footNav .shopCartButton b").show()};
    })
</script>
@yield("javascript")
</body>
</html>
