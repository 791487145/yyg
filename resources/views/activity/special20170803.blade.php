<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>江西风味-易游购</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<meta name="keywords" content="易游购,旅游特产购物,旅游购,易游,特产购物">
		<meta name="description" content="易游购是江西九江聚思味食品有限公司的一款互联网产品，成立于2015年11月，致力于打造一家立足于旅游行业的互联网企业，预计在未来五年内，实现全国旅游购物互联网化。">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-control" content="no-cache">
		<meta http-equiv="Cache" content="no-cache">
		<style>
			a{-webkit-tap-highlight-color:transparent;}
			body,html,img,a{padding: 0;margin: 0;border: 0;
			background: #f2f0f3;
			}
			img{width: 100%;border: 0;padding: 0;margin: 0;margin-bottom: -5px;}
			.ios,.android{display: none;}
			
		</style>
	</head>
	<body>
		<div class="imgbox">
			<div class="weixin" >
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_01.png" />
				<a href="/goods/464"><img src="http://www.yyougo.com/app/special/img/20170803/20170803_02.png" /><img src="http://www.yyougo.com/app/special/img/20170803/20170803_03.png" /></a>

				<a href="/goods/466"><img src="http://www.yyougo.com/app/special/img/20170803/20170803_04.png" /><img src="http://www.yyougo.com/app/special/img/20170803/20170803_05.png" /></a>

				<a href="/goods/465"><img src="http://www.yyougo.com/app/special/img/20170803/20170803_06.png"/><img src="http://www.yyougo.com/app/special/img/20170803/20170803_07.png" /></a>

				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_08.png"/>
			</div>
			<div class="ios">
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_01.png"/>
				<a href="yyg://loadGoodsUrl/464"><img src="http://www.yyougo.com/app/special/img/20170803/20170803_02.png"/><img src="http://www.yyougo.com/app/special/img/20170803/20170803_03.png" /></a>

				<a href="yyg://loadGoodsUrl/466"><img src="http://www.yyougo.com/app/special/img/20170803/20170803_04.png"/><img src="http://www.yyougo.com/app/special/img/20170803/20170803_05.png" /></a>

				<a href="yyg://loadGoodsUrl/465"><img src="http://www.yyougo.com/app/special/img/20170803/20170803_06.png"/><img src="http://www.yyougo.com/app/special/img/20170803/20170803_07.png" /></a>

				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_08.png" />
			</div>
			<div class="android">
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_01.png" />
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_02.png" onclick="imagelistner(464)"/>
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_03.png" onclick="imagelistner(464)"/>
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_04.png" onclick="imagelistner(466)"/>
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_05.png" onclick="imagelistner(466)"/>
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_06.png" onclick="imagelistner(465)"/>
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_07.png" onclick="imagelistner(465)"/>
				<img src="http://www.yyougo.com/app/special/img/20170803/20170803_08.png"/>
			</div>
		</div>
	</body>
    <script type="text/javascript">
    	var weixin = document.getElementsByClassName("weixin");
    	var ios = document.getElementsByClassName("ios");
    	var android = document.getElementsByClassName("android");
    	//获取链接叁数
		function getParameter(name){
		    var reg = new RegExp("(^|&|@)" + name + "=([^&@]*)(@|&|$)"); //构造一个含有目标参数的正则表达式对象
		    var str = window.location.search.substr(1);
		    	str = str.split("?");
		    var r = str[0].match(reg);  //匹配目标参数
		    if (r != null) return unescape(r[2]); return null; //返回参数值
		};
		var type = getParameter("from");
		for(var i=0;i < ios.length; i++){
			if(type == "ios"){
				ios[i].style.display="block";
				weixin[i].style.display="none";
				android[i].style.display="none";
			}else if(type == "android"){
				ios[i].style.display="none";
				weixin[i].style.display="none";
				android[i].style.display="block";
			}else{
		        ios[i].style.display="none";
				weixin[i].style.display="block";
				android[i].style.display="none";  
			}
		}
        function imagelistner(n){ //未认证调用app方法
            js.method(n);
        }
    </script>
</html>
