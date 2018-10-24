 <!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>{{$data['nick_name']}}的微商城</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<meta name="keywords" content="易游购,旅游特产购物,旅游购,易游,特产购物">
		<meta name="description" content="易游购是江西九江聚思味食品有限公司的一款互联网产品，成立于2015年11月，致力于打造一家立足于旅游行业的互联网企业，预计在未来五年内，实现全国旅游购物互联网化。">
		<style>
			body{text-align: center;padding: 20px 0;}
			.colorA{color: #ED6B09;}
			.marginAotu{margin: 0 auto;}
			.codeImg{margin: 20px auto; width: 70%;display: block;}
			.text{color: #999;margin: 20px auto;font-size: 14px;}
		</style>
	</head>
	<body>
		<div class="colorA">{{$data['nick_name']}}的微商城</div>
		<img class="codeImg" src="{{$data['wx_qrcode']}}">
		<div style="font-size: 16px;">长按识别二维码</div>
		<div class="text">
			易游购旅游特产购物平台，千挑万选，只为<br />您提供最好的商品品质和服务
		</div>
	</body>
</html>
