@extends('travel')
@section('content')
<style> 
    .statisNum{background: url({{url('/images/statisNum_bg.png')}}) no-repeat;}
    .setDiv{position: fixed;background: rgba(0,0,0,0.5);width: 100%;height: 100%;z-index: 2;}
    .form{position: absolute;left: 50%;top: 50%;transform: translate(-50%, -50%);background: #fff;}
    .form h4{font-size:18px;background: #E3E3E3;padding: 10px 20px;}
    .form .box{padding: 20px;width:620px;}
    .form select{width:100px;padding: 4px;}
    .Preservation{padding: 8px 20px;background:#e7641c;color: #FFF;display:block;margin: 20px auto;border: 0;cursor: pointer;}
    .inputText{width: 184px; height: 30px;border: 1px solid #6e6e6e;padding: 0 8px;margin-right: 14px;}
    .form .fileUpload {position: relative;display: inline-block;width: 100px;height: 100px;background: url(../images/file_icon_03.png);float: left;background-size: 100px;}
	.deletePopup{float: right;font-size: 16px;cursor: pointer;}
	.rankingTitle{padding: 20px 30px;border-bottom: 1px solid #E0E0E0;}
	.rankingTitle span:nth-child(1){color: #666;}
	.rankingTitle span:nth-child(2){color: #e7641c;float: right;cursor: pointer;}
	.statusTab{padding: 0 20px;margin: 0;height: auto;}
	.statusTab span{width:24%;margin: 0 4%;text-align: center;padding:5px 0;}
	.statusTab span a{color: #666;}
	.rightCon .detailTable th {height: 56px;background: #fff;padding: 0 10px;}
	
</style>
@if($travel->ta_name=='' || $travel->ta_logo == '')
<div class="setDiv">
	<form class="form" method="post" action="{{url('travel/AlertSetting')}}">
		<h4>个人设置 <span class="deletePopup">x</span></h4>
		<div class="box">
            <table>
                <tr>
                    <th>旅行社名称：</th>
                    <td><input class="inputText" type="text" name="ta_name" readonly="readonly" value="{{$travel->ta_name}}"/></td>
                    <th>手机号码：</th>
                    <td><input class="inputText" type="number" name="mobile" readonly="readonly" value="{{$travel->mobile}}"/></td>
                </tr>
                <tr>
                	<th>&nbsp;</th>
                </tr>
                <tr>
                	<th>&nbsp;</th>
                </tr>
                <tr>
                    <th>旅行社logo：</th>
                    <td>
                    <!-- 图片放置框 -->
                    <div style="overflow: hidden;">
                        <ul id="ul_pic" class="ul_pics clearfix" style="float:left;margin-right:10px;">
                        	@if($travel->ta_logo != '')
                        	<div class="new-imgbox">
                        		<div class="images-div photos">
                        			<input class="image_name" type="hidden" name="ta_logo" value="{{$travel->ta_logo}}">
                    				<img width="100" height="100" src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$travel->ta_logo}}">
                    				<span id="wq" class="img-del"></span>
                        		</div>
                        	</div>
                        	@endif
                        </ul>
                        <div style="float:left;overflow: hidden;">
                            <button id="coverbrowse" class="fileUploadIcon"></button>
                        </div>
                    </div>
                    <div style="color: #999;">建议上传120*120的图片</div>
                    </td>
                </tr>
                <tr>
                	<th>&nbsp;</th>
                </tr>
                <tr>
                    <th>所在地：</th>
                    <td>
                        <select name="travel_province_id" class="input-text supplier-select">
                            <option value="0">选择省份</option>
                            <option value="2">省份</option>
                        </select>
                        <select name="travel_city_id" class="input-text supplier-select">
                            <option value="0">选择市</option>
                        </select>
                    </td>
                </tr>
            </table>
            <input type="button" class="Preservation" onclick="Verification();" value="保存"/>
            <input type="submit" class="submitButton" style="display: none;">
        </div>
	</form>
</div>
@endif
<div class="rightCon">
    <div class="wrap">
        <h2><span>概况</span><a href="{{url('auth/logout')}}" class="headR">退出</a></h2>
        <div class="box">
            <div class="statisNum">
                <div><b>{{$data['orderToday']}}</b></div>
                <div>今日销售额</div>
            </div>
            <div class="statisNum">
                <div><b>{{$data['totalSales']}}</b></div>
                <div>累计销售额</div>
            </div>
            <div class="statisNum">
                <div><b>{{$data['guideToday']}}</b></div>
                <div>今日新增绑定导游</div>
            </div>
            <div class="statisNum">
                <div><b>{{$data['guideCount']}}</b></div>
                <div>已绑定导游总数</div>
            </div>
        </div>
    </div>
    <div style="margin-top:15px;">
    	<div style="width: 49.5%;margin-right:1%;background: #fff;float: left;">
        	<div class="rankingTitle"><span>按绑定游客数排行[排名前20]</span><span onclick="exportLefts()">导出当前状态全部数据Excel</span></div>
            <div class="statusTab">
                <a href="{{url('/dashboard/numAndSale/lefts0')}}"><span @if($lefts == 0) class="active" @endif>今日排行榜</span></a>
                <a href="{{url('/dashboard/numAndSale/lefts1')}}"><span @if($lefts == 1) class="active" @endif>本周排行榜</span></a>
                <a href="{{url('/dashboard/numAndSale/lefts2')}}"><span @if($lefts == 2) class="active" @endif>本月排行榜</span></a>
            </div>
            <div class="tabBox">
                <div class="active">
                    <table class="detailTable detailTable1">
                        <tr>
                            <th>排名</th>
                            <th>导游姓名</th>
                            <th>手机号</th>
                            <th>已绑定的游客数</th>
                        </tr>
                        @if(!empty($numsAndSalesInfo['guidersinfo']))
                            @foreach($numsAndSalesInfo['guidersinfo'] as $key=>$guider)
                                <!-- 去掉借过来的导游 -->
                                @if(!empty($guider))
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$guider['name']}}</td>
                                        <td>{{$guider['mobile']}}</td>
                                        <td>{{$guider['vistorsnum']}}</td>
                                    </tr>
                               @endif 
                            @endforeach
                        @endif
                    </table>
                    <a href="##" onclick="getLeftAllInfo()" style="text-align: center;color: #e7641c;padding: 10px;display: block;"><!-- 查看全部 --></a>
                </div>
            </div>
        </div>
        <div style="width: 49.5%;background: #fff;float: left;">
        	<div class="rankingTitle"><span>按销售额排行[排名前20]</span><span onclick="exportRight()">导出当前状态全部数据Excel</span></div>
            <div class="statusTab">
                <a href="{{url('dashboard/numAndSale/right0')}}"><span @if($right == 0) class="active" @endif>今日排行榜</span></a>
                <a href="{{url('dashboard/numAndSale/right1')}}"><span @if($right == 1) class="active" @endif>本周排行榜</span></a>
                <a href="{{url('dashboard/numAndSale/right2')}}"><span @if($right == 2) class="active" @endif>本月排行榜</span></a>
            </div>
            <div class="tabBox">
                <div class="active">
                    <table class="detailTable detailTable2">
                        <tr>
                            <th>排名</th>
                            <th>导游姓名</th>
                            <th>手机号</th>
                            <th>销量  (笔) </th>
                            <th>销售额 (元) </th>
                        </tr>
                        @if(!empty($numsAndSalesInfo['salesinfo']))
                            @foreach($numsAndSalesInfo['salesinfo'] as $k=>$sale)
                            <!-- 去掉借过来的导游 -->
                                @if(!empty($sale))    
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{$sale['name']}}</td>
                                        <td>{{$sale['mobile']}}</td>
                                        <td>{{$sale['sales_num']}}</td>
                                        <td>{{$sale['totalsales']}}</td>
                                    </tr>
                                @endif    
                            @endforeach 
                        @endif              
                    </table>
                    <a href="##" onclick="getRightAllInfo()" style="text-align: center;color: #e7641c;padding: 10px;display: block;"><!-- 查看全部 --></a>
                </div>
            </div>
        </div>
        
        <div style="width: 49.5%;margin-right:1%;background: #fff;float: left;margin-top:20px;">
        	<div class="rankingTitle"><span>按游客关注公众号人数[排名前20]</span><span onclick="exportBotom()">导出当前状态全部数据Excel</span></div>
            <div class="statusTab">
                <a href="{{url('/dashboard/numAndSale/botom0')}}"><span @if($botom == 0) class="active" @endif>今日排行榜</span></a>
                <a href="{{url('/dashboard/numAndSale/botom1')}}"><span @if($botom == 1) class="active" @endif>本周排行榜</span></a>
                <a href="{{url('/dashboard/numAndSale/botom2')}}"><span @if($botom == 2) class="active" @endif>本月排行榜</span></a>
            </div>
            <div class="tabBox">
                <div class="active">
                    <table class="detailTable detailTable3">
                        <tr>
                            <th>排名</th>
                            <th>导游姓名</th>
                            <th>手机号</th>
                            <th>关注公众号人数</th>
                        </tr>
                        @if(!empty($numsAndSalesInfo['refsinfo']))
                            @foreach($numsAndSalesInfo['refsinfo'] as $key=>$ref)
                                <!-- 去掉借过来的导游 -->
                                @if(!empty($ref))
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$ref['name']}}</td>
                                        <td>{{$ref['mobile']}}</td>
                                        <td>{{$ref['refNum']}}</td>
                                    </tr>
                               @endif 
                            @endforeach
                        @endif
                    </table>
                    <a href="##" onclick="getLeftAllInfo()" style="text-align: center;color: #e7641c;padding: 10px;display: block;"><!-- 查看全部 --></a>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{asset('lib/plupload/plupload.full.min.js')}}"></script>  
<script type="text/javascript">
$(function () {
	$(".deletePopup").click(function(){
		$(".setDiv").remove();
	});
    //获取省级
    province();
    function province(){
    	var html = '<option value="0">选择省份</option>';
        $.get('/travel/getCity/1',function(json){
            $.each(json,function(k,v){
                html += '<option value="'+v.id+'">'+v.name+'</option>';
            });
            $('[name=travel_province_id]').html(html);
        });
    }
    //获取市级
    $('[name=travel_province_id]').bind('change',function(){
        var province = $(this).val();
        var html = '<option value="0">选择市</option>';
        $.get('/travel/getCity/'+province,function(json){
            $.each(json,function(k,v){
                html += '<option value="'+v.id+'">'+v.name+'</option>';
            });
            $('[name=travel_city_id]').html(html);
        });
    });
    //旅行社LOGO上传
    var uploader = new plupload.Uploader({
        browse_button : 'coverbrowse', //触发文件选择对话框的按钮，为那个元素id
        url : '{{url('/travel/uploadPlupLoad')}}', //服务器端的上传页面地址
        //multi_selection: true,
        resize: {
      	    quality: 60,
      	},
        flash_swf_url : '{{asset('lib/plupload/Moxie.swf')}}', 
        silverlight_xap_url : '{{asset('lib/plupload/Moxie.xap')}}' 
    });    

    //在实例对象上调用init()方法进行初始化
    uploader.init();
    
    uploader.bind('FilesAdded',function(uploader,files){
    	var li = "<li id='singleupload'><div class='progress'><span class='bar'></span><span class='percent'>已上传  0%</span></div></li>";
        $("#ul_pic").html(li);
      	uploader.start(); 
    });

    //显示上传进度条
    uploader.bind('UploadProgress',function(uploader,file){
    	var percent = file.percent; 
   	    $("#" + file.id).find('.bar').css({"width":percent + "%"}); 
   	    $("#" + file.id).find(".percent").text("已上传"+ percent + "%");  
    }); 
    
    //上传完成后执行的操作
    uploader.bind('FileUploaded',function(uploader,file,responseObject){
	    var data = JSON.parse(responseObject.response);
        $("#ul_pic").html('<span style="display:inline-block" class="images-div photos">'+
	          '<input type="hidden" name="ta_logo" value="'+data.img+'">'+
	          '<img width="100" height="100" src="{{env("IMAGE_DISPLAY_DOMAIN")}}' +data.img+'" />');
        //'<span class="img-del"></span></span>' 
        //deleteimg();  
    });

    
});
    

    //旅行社logo删除
    function deleteimg(){
      	 $(".img-del").unbind().click(function(){
          	 $(this).parent().remove();
        	 })
    }

    //对弹出框层的验证
    function Verification(){
    	var ta_name = $('[name=ta_name]').val();
    	var mobile = $('[name=mobile]').val();
    	//var store_name = $('[name=store_name]').val();
    	var ta_logo = $('[name=ta_logo]').val();
    	var travel_province_id = $('[name=travel_province_id] option:selected').attr("value");
    	var travel_city_id = $('[name=travel_city_id] option:selected').attr("value");
    	var pattern = /^1[34578]\d{9}$/; 
    	if(!ta_name){
    		layer.msg("请填写旅行社名称");
    		return false;
    	}else if(!pattern.test(mobile)){
    		layer.msg("请填写正确的手机号")
    		return false;
    	}else if(!ta_logo){
    		layer.msg("请上传旅行社logo")
    		return false;
    	}else if(travel_province_id == "0"){
    		layer.msg("请选择省份")
    		return false;
    	}else if(travel_city_id == "0"){
    		layer.msg("请选择市区")
    		return false;
    	}
    	$(".submitButton").click();
    }

    //进行左边导出触发的函数
    function exportLefts(){
        var length = $('.detailTable1 td').length;
        if(length > 0){
        	var url = '{{url('/dashboard/export/lefts')}}';
            location.href = url;
        }else{
        	layer.alert("当前没有数据")
        }
        
    }

    //进行右边导出的触发函数
    function exportRight(state){
    	//url += "?<php echo $_SERVER['QUERY_STRING'];?>";
        var length = $('.detailTable2 td').length;
        if(length > 0){
        	var url = '{{url('/dashboard/export/right')}}';
            location.href = url;
        }else{
        	layer.alert("当前没有数据")
        }
    	
    }

    //进行下面导出的触发函数
    function exportBotom(){
    	//url += "?<php echo $_SERVER['QUERY_STRING'];?>";
        var length = $('.detailTable3 td').length;
        if(length > 0){
        	var url = '{{url('/dashboard/export/botom')}}';
            location.href = url;
        }else{
        	layer.alert("当前没有数据")
        }
    	
    }

    //进行查看全部时左边的信息
    function getLeftAllInfo(){
    	var url = '{{url('/dashboard/info/left')}}';
    	location.href = url;
    }

    //进行查看全部时右边的信息
    function getRightAllInfo(){
    	var url = '{{url('/dashboard/info/right')}}';
    	location.href = url;
    }
                    	
</script>    
    
    
    
@stop