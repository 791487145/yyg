@extends('layout_pop')
@section("content")
<style>
    li{
        float: left;
        list-style: none;
    }
    #order li{
        margin-left: 18%;
    }
    .order li{
        margin-left: 18%;
    }
    .all{
        margin-left: 10px;
        margin-top: 10px;
    }
    td{width:50%;border:0;}
    .color-line{height:4px;background: #12CC94;position: absolute;left:97px;top:40px;width: 75%;}
    .width1-4{width: 25%;text-align: center;line-height: 40px;}
    .commentBox{margin-top: 20px;padding-top: 20px; border-top: solid 1px #e8e8e8;}
    .commentBox .but{float: right;text-align: center;width: 80px;line-height: 30px;border: 1px solid #e8e8e8;border-radius: 3px;margin-right:20px;cursor: pointer;}
	.commentBox .butin{cursor:auto;}
</style>
    <div class="all">
        <div class="cl" style="position: relative;">
        	<div class="color-line">
        		<div style="width: 34%;height:4px;margin: 0 auto;background: #5068A9;"></div>
        	</div>
            @if(!empty($order_log))
                @foreach($order_log as $log)
                    <div class="f-l width1-4">
                        {{$log['action']}}<br />
                        {{$log['created_at']}}
                    </div>
                @endforeach
            @endif
        </div>
        <div>
            <div>
            	<p>订单信息</p>
                <table class="table table-border table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td>订单编号：{{$orderinfo->order_no}}</td>
                            <td>付款时间：{{$order_pay_finish}}</td>
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
                            <td>订单金额：{{$orderinfo->real_order_pay}}</td>
                            <td>配送方式：
                                @if($orderinfo->express_type == 0)
                                                                                                            快递
                                @else
                                                                                                            自提
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>商品信息</p>
                @if(!empty($ordergoodsinfo))
                @foreach($ordergoodsinfo as $goods)
                    <table class="table table-border table-bordered table-hover" style="margin-top: -1px;">
                        <tbody>
                            <tr>
                                <td>商品名称：{{$goods->goods_title}}</td>
                                <td>零售价：{{$goods->price}}</td>
                            </tr>
                            <tr>
                                <td>规格：{{$goods->spec_name}}</td>
                                <td>数量：{{$goods->num}}</td>
                            </tr>
                        </tbody>
                   </table>
                   <br>
                @endforeach
                @endif
                <p>赠品信息</p>  
                @if(!empty($goodsgiftinfo))
                   <table class="table table-border table-bordered table-hover">
                        @foreach($goodsgiftinfo as $goods_gift)
                            <tbody>
                            <tr class="success">
                                <td>商品名：{{$goods_gift->goods_title}}</td>
                                <td>规    格：{{$goods_gift->spec_name}}</td>
                                <td>零售价：{{$goods_gift->price}}</td>
                            </tr>
                            </tbody>
                        @endforeach
                   </table>     
                @endif
            <div>
                <div><label>收货人信息 </label>：{{$orderinfo->addr}}</div>
                <div><label>物流公司</label>：{{$orderinfo->express_name}}</div>
                <div>
                    <label>物流单号</label>：
                    @if($orderinfo->state == 5)
                        <input type="text" id="orderNumber" value="{{$orderinfo->express_no}}"> <a class="btn btn-success" href="https://www.baidu.com/s?wd={{$orderinfo->express_name}}+{{$orderinfo->express_no}}">手动查询物流信息</a>
                    @endif
                </div>
            </div>

            <div>
                <label>买家留言</label>
                <div>
                    <textarea class="textarea radius" readonly>
                        {{$orderinfo->remark}}
                    </textarea>
                </div>
            </div>
            @if(!empty($comments))
            @foreach($comments as $key=>$val)
            <div class="commentBox">
                <label>商品名称：{{$val->goods_title}}</label>&emsp;&emsp; <!-- <label>规格：</label> -->
                <div>评价内容</div>
                <div>{{$val->comment->created_at}}</div>
                <div style="overflow: hidden;">
                   <div style="width:80%;float: left;">{{$val->comment->comment}}</div>
	               <span class="but but1{{$key}}" style="@if($val->comment->state == 2) display: none; @endif" onclick="comment({{$val->comment->id}},{{$key}})" >隐藏不显示</span>
	               <span class="but but2{{$key}}" style="@if($val->comment->state == 1) display: none; @endif" onclick="comment({{$val->comment->id}},{{$key}})">取消隐藏</span>
                </div>
                <br />
                <div class="photos">
                @if(!empty($val->commment_img))
                @foreach($val->commment_img as $img)
                	<img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$img->image_name}}" width="100" height="100"/>
                @endforeach
                @endif
                </div>
                @if(!empty($val->comment->reply_comment))
	                <div>回复内容：</div>
	                <div>{{$val->comment->reply_comment}}</div>
		        @endif
            </div>
            @endforeach
            @endif
            <div class="text-c">
            	<button type="button" onclick="a()" class="btn btn-success radius mt-20" id="user-save" name="route-save"><i class="icon-ok"></i> 返回</button>
            </div>
        </div>

    </div>


@endsection
@section("javascript")
<script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
<script>
    layer.photos({
        photos: '.photos'
        ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
    });
    /*弹窗操作*/
    function dialogs(title,url,w,h){
        layer_show(title,url,w,h);
    }

    function edit(obj){
        var val = $("#num_water_"+obj).val();
        $.post("/goods/numsold/edit",{specId:obj,num_water:val},function(data){
            layer.msg(data.msg,{icon:1,time:1000});
        })
    }
</script>
<script>
	function comment(id,key){
		var commentid = id;
		layer.confirm('确定要改变当前评价的状态吗？', {
		  btn: ['取消','确定'], //按钮
		  title:"评价设置"
		}, function(index){
			layer.close(index);
		}, function(index){
			$.post("{{url('/comment/changestate')}}",{'commentid':commentid},function(data){
			    if(data.ret == 'yes'){
					if(data.state==2){
						$(".but1"+key).hide();
					    $(".but2"+key).show();
					}else{
						$(".but2"+key).hide();
					    $(".but1"+key).show();
					}
					layer.msg(data.msg);
				}else{
					layer.msg(data.msg);
				}
			},"JSON")
		});
	}

	
    function a(){
        layer_close();
    }
    
	$(function(){
		$("#form-perm-user-edit").Validform({
	        tiptype:2,
	        ajaxPost:true,
	        postonce:true,
	        callback:function(data){
	            if(data.ret == 'yes') {
	                layer.alert(data.msg,{icon:1,time:1000});
	                parent.location.replace(parent.location.href);
	            } else if(data.ret == 'no') {
	                layer.alert(data.msg,{icon:2,time:5000});
	            } else {
	                layer.alert('添加失败', {icon:2,time:5000});
	            }
	        }
	    });
	});
  function save(url){
      var express_no = $("#orderNumber").val();
      $.ajax({
          method:'post',
          url:url,
          data:{express_no:express_no},
          success:function(data){
              parent.location.replace(parent.location.href);
          }
      });
  }
</script>
@endsection