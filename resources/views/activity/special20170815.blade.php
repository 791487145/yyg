<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>新鲜水果专享日-易游购</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<meta name="keywords" content="易游购,旅游特产购物,旅游购,易游,特产购物">
		<meta name="description" content="易游购是江西九江聚思味食品有限公司的一款互联网产品，成立于2015年11月，致力于打造一家立足于旅游行业的互联网企业，预计在未来五年内，实现全国旅游购物互联网化。">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-control" content="no-cache">
		<meta http-equiv="Cache" content="no-cache">
		<link href="http://www.yyougo.com/img/logoIcon.ico" rel="Shortcut Icon">
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
				<img src="http://img2.yyougo.com/20170815_01.png?imageslim"/>
				<img src="http://img2.yyougo.com/20170815_02.png?imageslim"/>
				<a href="http://h5.yyougo.com/goods/491"><img src="http://img2.yyougo.com/20170815_03.png?imageslim"/></a>
				<a href="http://h5.yyougo.com/goods/468"><img src="http://img2.yyougo.com/20170815_04.png?imageslim"/></a>
				<a href="http://h5.yyougo.com/goods/492"><img src="http://img2.yyougo.com/20170815_05.png?imageslim"/></a>
				<a href="http://h5.yyougo.com/goods/484"><img src="http://img2.yyougo.com/20170815_06.png?imageslim"/></a>
				<a href="http://h5.yyougo.com/goods/435"><img src="http://img2.yyougo.com/20170815_07.png?imageslim"/></a>
				<img src="http://img2.yyougo.com/20170815_08.png?imageslim"/>
			</div>
			<div class="ios">
				<img src="http://img2.yyougo.com/20170815_01.png?imageslim"/>
				<img src="http://img2.yyougo.com/20170815_02.png?imageslim"/>
				<a href="yyg://loadGoodsUrl/491"><img src="http://img2.yyougo.com/20170815_03.png?imageslim"/></a>
				<a href="yyg://loadGoodsUrl/468"><img src="http://img2.yyougo.com/20170815_04.png?imageslim"/></a>
				<a href="yyg://loadGoodsUrl/492"><img src="http://img2.yyougo.com/20170815_05.png?imageslim"/></a>
				<a href="yyg://loadGoodsUrl/484"><img src="http://img2.yyougo.com/20170815_06.png?imageslim"/></a>
				<a href="yyg://loadGoodsUrl/435"><img src="http://img2.yyougo.com/20170815_07.png?imageslim"/></a>
				<img src="http://img2.yyougo.com/20170815_08.png?imageslim"/>
			</div>
			<div class="android">
				<img src="http://img2.yyougo.com/20170815_01.png?imageslim" />
				<img src="http://img2.yyougo.com/20170815_02.png?imageslim" />
				<img src="http://img2.yyougo.com/20170815_03.png?imageslim" onclick="imagelistner(491)"/>
				<img src="http://img2.yyougo.com/20170815_04.png?imageslim" onclick="imagelistner(468)"/>
				<img src="http://img2.yyougo.com/20170815_05.png?imageslim" onclick="imagelistner(492)"/>
				<img src="http://img2.yyougo.com/20170815_06.png?imageslim" onclick="imagelistner(484)"/>
				<img src="http://img2.yyougo.com/20170815_07.png?imageslim" onclick="imagelistner(435)"/>
				<img src="http://img2.yyougo.com/20170815_08.png?imageslim" />
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
