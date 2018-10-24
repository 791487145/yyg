@extends('wx.layout')
@section('title')
    售后详情
@endsection
@section('content')
<style>
	.afterSalesBox{background: #fff;border-radius:5px;margin:20px 0;padding: 10px 0;}
	.afterSalesBox .title{padding: 10px;border-bottom: 1px solid #e8e8e8;overflow: hidden;}
	.afterSalesBox .title span{color: #999;float: right;}
	.afterSalesBox .content{padding: 10px;}
	.afterSalesBox .content .imgBox{overflow: hidden;}
	.afterSalesBox .content .imgBox img{float: left;margin-right: 10px;}
</style>
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">售后详情</div>
</div>
    @foreach($orderreturninfos as $orderreturninfo)
        <div class="afterSales">
            <div class="afterSalesBox">
                <div class="title">买家发起退款申请<span>{{$orderreturninfo->created_at}}</span></div>
                <div class="content commentBox">
                    <p>{{$orderreturninfo->return_content}}</p>
                    <div class="commentImgBox imgPopup">
                        @foreach($orderreturninfo->ReturnImgs as $key=>$ReturnImgs)
                            <img data-index="{{$key}}" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ReturnImgs->name}}?imageslim">
                        @endforeach
                    </div>
                </div>
            </div>

            @if($orderreturninfo->state == 1)
                <div class="afterSalesBox">
                    <div class="title">卖家同意退款<span>{{$orderreturninfo->created_at}}</span></div>
                    <div class="content">
                        <p>卖家同意了您的退款申请，待退款金额<span>{{$orderreturninfo->amount}}</span>元。</p>
                    </div>
                </div>
            @endif
            @if($orderreturninfo->state == 3)
                <div class="afterSalesBox">
                    <div class="title">卖家同意退款<span>{{$orderreturninfo->created_at}}</span></div>
                    <div class="content">
                        <p>卖家同意了您的退款申请，待退款金额<span>{{$orderreturninfo->amount}}</span>元。</p>
                    </div>
                </div>
                <div class="afterSalesBox">
                    <div class="title">退款成功<span>{{$orderreturninfo->updated_at}}</span></div>
                    <div class="content">
                        <p>退款成功，收到退款金额<span>{{$orderreturninfo->amount}}</span>元。</p>
                    </div>
                </div>
            @endif
            @if($orderreturninfo->state == 4)
                <div class="afterSalesBox">
                    <div class="title">卖家已驳回退款申请<span>{{$orderreturninfo->updated_at}}</span></div>
                    <div class="content">
                        <p>{{$orderreturninfo->return_reason}}</p>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
@endsection