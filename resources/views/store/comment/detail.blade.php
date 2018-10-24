@extends('supplier')
@section('content')
	<link rel="stylesheet" href="{{asset('lib/imgbox/css/lrtk.css')}}" />
    <script type="text/javascript" src="{{asset('lib/imgbox/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/imgbox/js/jquery.imgbox.pack.js')}}"></script>
	<style>
		.color-line{height:4px;background: #12CC94;position: absolute;left:12.5%;top:40px;width: 75%;}
    	.width1-4{width:25%;text-align: center;line-height: 40px;}
		#orderNumber{width: 184px;
		    height: 30px;
		    border: 1px solid #6e6e6e;
		    padding: 0 8px;
		    margin-right: 14px;
		}
    	.rightCon .btn-success{
		    display: inline-block;
		    padding: 0 4px;
		    height: 32px;
		    line-height: 30px;
		    color: #fff;
		    background: #e7641c;
		    font-size: 14px;
		    cursor: pointer;
		}
		.commentBox{margin-top: 20px;padding-top: 20px; border-top: solid 1px #e8e8e8;}
	    .commentBox .but{float: right;text-align: center;width: 80px;line-height: 30px;border: 1px solid #e8e8e8;border-radius: 3px;margin-right:20px;cursor: pointer;}
		.commentBox .butin{cursor:auto;}
		.commentText{width:250px;height:100px;}
	</style>
	
    <div class="rightCon">
        <div class="wrap orderDetail .form">
            <h2><span>订单详情</span></h2>
            	<div class="cl" style="position: relative;overflow: hidden;">
            		<div class="color-line">
		        		<div style="width: 34%;height:4px;margin: 0 auto;background: #5068A9;"></div>
		        	</div>
		            @if(!empty($order_log))
		                @foreach($order_log as $log)
		                    <div class="float-left width1-4">
		                        {{$log['action']}}<br />
		                        {{$log['created_at']}}
		                    </div>
		                @endforeach
		            @endif
		        </div>
                <div class="box">
                    <h5>订单信息</h5>
                    <table style="padding: 10px;">
                        <tr>
                            <td width="400">订单编号：{{$orderinfo->order_no}}</td>
                            <td width="400">付款时间：{{$order_pay_finish}}</td>
                        </tr>
                        <tr>
                            <td>付款方式：
                                @if($orderinfo->pay_type == 1) 
                                    ping++支付宝支付 
                                @elseif($orderinfo->pay_type == 2) 
                                    ping++微信支付 
                                @else
                                                                                                             微信商户支付                                                                     
                                @endif
                            </td>
                            <td>运&emsp;&emsp;费：{{$orderinfo->amount_express}}</td>
                        </tr>
                        <tr>
                            <td>订单金额：<span style="color: red;">￥{{$orderinfo->real_order_pay}}</span></td>
                            <td>配送方式：
                                @if($orderinfo->express_type == 0)
                                                                                                            快递
                                @else
                                                                                                            自提
                                @endif
                            </td>
                        </tr>

                    </table>

                </div>
                <div class="box">
                    <h5>商品信息</h5>
                    @if(!empty($ordergoodsinfo))
                    @foreach($ordergoodsinfo as $goods)
                    <table style="padding: 10px;">
                        <tr>
                            <td width="400"><p class="limitText">商品名称：{{$goods->goods_title}}</p></td>
                            <td width="400">零售价：{{$goods->price}}</td>
                        </tr>
                        <tr>
                            <td>规格：{{$goods->spec_name}}</td>
                            <td>数量：{{$goods->num}}</td>
                        </tr>
                    </table>
                    @endforeach
                    @endif
                </div>
                <div class="box">
                    <h5>赠品信息</h5>
                    @if(!empty($goodsgiftinfo))
                    @foreach($goodsgiftinfo as $goods_gift)
                    <table style="padding: 10px;">
                        <tr>
                            <td width="400"><p class="limitText">商品名称：{{$goods_gift->goods_title}}</p></td>
                            <td width="400">零售价：{{$goods_gift->price}}</td>
                        </tr>
                        <tr>
                            <td>规格：{{$goods_gift->spec_name}}</td>
                            <td>数量：{{$goods_gift->num}}</td>
                        </tr>
                    </table>
                    @endforeach
                    @endif
                </div>
                <div class="box">
                    <div><label>收货人信息</label>：{{$orderinfo->addr}}</div><br />
                    <div><label>物流公司</label>：{{$orderinfo->express_name}}</div><br />
                    <div>
                        <label>物流单号</label>：
                        @if($orderinfo->state == 5)
                            <input type="text" id="orderNumber" value="{{$orderinfo->express_no}}" disabled="disabled"> 
                            <a class="btn-success" href="https://www.baidu.com/s?wd={{$orderinfo->express_name}}+{{$orderinfo->express_no}}">手动查询物流信息</a>
                        @endif
                    </div>
                </div>
                <div class="box">
                    <label>买家留言：</label>
                    <div>
                        <textarea class=" radius" style="width:600px;height:150px;" readonly>{{$orderinfo->remark}}</textarea>
                    </div>
                    @if(!empty($goodsComments))
                    @foreach($goodsComments as $key=>$val)
	                <div class="commentBox">
		                <label style="font-weight: bold;">商品名称：{{$val->goods_title}}</label>
		                <div class="margin-top10 margin-bottom10">评价内容：</div>
		                <div class="color9 margin-bottom10">{{$val->comment->created_at}}</div>
		                <div style="overflow: hidden;">
		                	<div class="color9" style="width:80%;float: left;">{{$val->comment->comment}}</div>
		                	@if(empty($val->comment->reply_comment))
		                	    <span class="but but{{$key}}" onclick="comment({{$val->comment->id}},{{$key}})">去回复</span>
		                	@else
		                	    <span class="but color9">已回复</span>
		                	@endif
		                </div>
		                <br />
		                <div>
		                @if(!empty($val->commment_img))
		                @foreach($val->commment_img as $img)
		                <a href="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$img->image_name}}" class="goods-imgbox">
		                	<img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$img->image_name}}" width="100" height="100"/>
		                </a>
		                @endforeach
		                @endif	
		                </div>
		                <div class="replyComment{{$key}}">
		                	@if(!empty($val->comment->reply_comment))
	    		                <div class="margin-top10 margin-bottom10">回复内容：</div>
	    		                <div class="color9">{{$val->comment->reply_comment}}</div>
			                @endif
		                </div>
		            </div>
		            @endforeach
		            @endif
		            
	            </div>
            <div class="footButton">
	        	<input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
	        </div>
        </div>
       
    </div>
    <script type="text/javascript">
       $(function() {
            $(".goods-imgbox").imgbox({
                'speedIn'		: 0,
                'speedOut'		: 0,
                'alignment'		: 'center',
                'overlayShow'	: true,
                'allowMultiple'	: false
            });
        });
    	function comment(id,key){
        	var commitid = id;
			var textarea = '<textarea class="textarea radius commentText"></textarea>';
			layer.confirm(textarea, {
			  btn: ['取消','确定'], //按钮
			  title:"评价回复"
			}, function(index){
				layer.close(index);
			}, function(index){
				var val = $(".commentText").val();
				if(val){
					//layer.msg("回复成功");
					$.post("{{url('/comment/reply')}}",{'commitid':commitid,'reply_val':val},function(data){
						    if(data.ret == 'yes'){
						    	layer.msg(data.msg);
						    	var replyComment = '<div class="margin-top10 margin-bottom10">回复内容：</div><div class="color9">'+val+'</div>'
						    	$(".replyComment"+key).html(replyComment);
						    	$(".but"+key).addClass("color9").removeAttr("onclick").text('已回复');
							}else{
								layer.msg(data.msg);
							}
// 			                parent.location.replace(parent.location.href);
					},"json")
				}else{
					layer.msg("请填写评价回复");
					return false;
				}
			});
		}
   </script>
@stop