<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>404-易游购</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <meta name="keywords" content="易游购,旅游特产购物,旅游购,易游,特产购物">
    <meta name="description" content="易游购是江西九江聚思味食品有限公司的一款互联网产品，成立于2015年11月，致力于打造一家立足于旅游行业的互联网企业，预计在未来五年内，实现全国旅游购物互联网化。">
    <link href="/images/logoIcon.ico" rel="Shortcut Icon">
    <style>
        #contentBox{margin-top:100px;color: #FFFFFF;text-align: center;position: fixed;top: 0;left: 0;width: 100%;}
        /*CSS代码片段*/
        html,body{
            background-repeat:repeat-y;
            margin:0;
            padding: 0;
            height: 100%;
            overflow:hidden;
            font-family:'microsoft yahei',Arial,sans-serif;
            filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);
            -ms-filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);/*IE8*/
            background:red; /* 一些不支持背景渐变的浏览器 */
            background:-moz-linear-gradient(top,#85a4f3,#ff8f04);
            background:-webkit-gradient(linear, 0 0, 0 bottom, from(#85a4f3), to(#ff8f04));
            background:-o-linear-gradient(top,#85a4f3,#ff8f04);
        }
        .button{text-decoration: none;border-radius: 5px;padding: 8px 20px;color: #ff8f04;background: #fff;border: 1px solid #fff;font-size: 16px;}
    </style>
</head>
<body onLoad="showCloud();">
<div id="contentBox">
    <!--<div style="font-size: 60px;">π_π</div>-->
    <div style="font-size:5rem;margin-bottom:50px;">404</div>
    <div style="font-size:18px;margin-bottom:50px;">很抱歉，您所访问的页面不存在！</div>
    <a class="button" href="/" >返回首页</a>
</div>
</body>
<script type="text/javascript">
    /*Javascript代码片段*/
    //创建画布并开始动画
    function showCloud(){
        //创建画布设置画布属性
        var canvas = document.createElement("canvas");
        canvas.width = document.body.clientWidth;
        canvas.height = document.body.clientHeight;
//			canvas.style.position = "absolute";
        canvas.style.zIndex = 0;
        var contentBox = document.getElementById("contentBox");
        contentBox.style.marginTop = canvas.height*25/100+"px";
        var ctx = canvas.getContext("2d");
        //向body中添加画布
        document.body.appendChild(canvas);
        //设置一个初始X轴位置
        var i = 50;
        //循环更新画布
        window.setInterval(function() {
            //清空画布
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            //绘制一朵云
            drawCloud(ctx, i, canvas.width * 0.1, canvas.width * 0.1);
            drawCloud(ctx, (canvas.width * 0.8)+i, canvas.height * 0.2, canvas.width * 0.1);
            drawCloud(ctx, (canvas.width * 0.1)+i, canvas.height * 0.7, canvas.width * 0.25);
            drawCloud(ctx, (canvas.width * 0.5)+i, canvas.height * 0.6, canvas.width * 0.15);
            drawCloud(ctx, (canvas.width * 0.2)+i, canvas.height * 1, canvas.width * 0.4);
            drawCloud(ctx, (canvas.width * 0.6)+i, canvas.height * 1.05, canvas.width * 0.4);
            drawCloud(ctx, (canvas.width * 0.3)+i, canvas.height * 0.6, canvas.width * 0.15);
            //云朵向右随机移动
            i += Math.random();
        },80)
    }


    /*渲染单个云朵
     context:  canvas.getContext("2d")对象
     cx: 云朵X轴位置
     cy: 云朵Y轴位置
     cw: 云朵宽度
     */
    function drawCloud(context, cx, cy, cw) {
        //云朵移动范围即画布宽度
        var maxWidth = context.canvas.width;
        var maxHeight = context.canvas.height;
        //如果超过边界从头开始绘制
        cx = cx % maxWidth;
        //云朵高度为宽度的60%
        var ch = cw * 0.6;
        //开始绘制云朵
        context.beginPath();
        context.fillStyle = "white";
        //创建渐变
        var grd = context.createLinearGradient(0, 0, 0, cy);
        grd.addColorStop(0, 'rgba(255,255,255,0.8)');
        grd.addColorStop(1, 'rgba(255,255,255,0.5)');
        context.fillStyle = grd;
        context.fill();
        //在不同位置创建5个圆拼接成云朵现状
        context.arc(cx, cy, cw * 0.19, 0, 360, false);
        context.arc(cx + cw * 0.08, cy - ch * 0.3, cw * 0.11, 0, 360, false);
        context.arc(cx + cw * 0.3, cy - ch * 0.25, cw * 0.25, 0, 360, false);
        context.arc(cx + cw * 0.6, cy, cw * 0.21, 0, 360, false);
        context.arc(cx + cw * 0.3, cy - ch * 0.1, cw * 0.28, 0, 360, false);
        context.closePath();
        context.fill();
    }
</script>
</html>
