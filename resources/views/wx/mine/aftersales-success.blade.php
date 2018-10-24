@extends('wx.layout')
@section('title')
    售后提交成功
@endsection
@section('content')
<div class="successWrap">
    <div class="lineB info">
        <div class="successTop">恭喜! 售后申请提交成功！<br>我们将在1-2个工作日内完成审核，请耐心等待～</div>
        <div class="conButtonGroup">
            <span class="left"><a href="/aftersalesDetail/<?php echo $orderno?>" class="btnYellow">查看售后详情</a></span>
            <span class="right"><a href="/" class="btnOrange">返回首页</a></span>
        </div>
    </div>
    <div class="orderInfo">
        <p>客服电话：<span>400-9158-971</span><a href="tel:400-9158-971" class="copyBtn">拨打</a></p>
        <p>微信：<span id="copyTxt">yyougou2015</span><a href="javascript:void(0)" class="copyBtn" id="copyBtn" data-clipboard-action="copy" data-clipboard-target="#copyTxt">复制</a></p>
    </div>

</div>
@endsection
@section('javascript')
    <script type="text/javascript" src="/wx/js/clipboard.min.js"></script>
    <script>
        $(".copyBtn").click(function(){
            var info = '复制成功';
            information(info);
        })
        var clipboard = new Clipboard('#copyBtn');
        clipboard.on('success', function(e) {
            console.log(e);
        });
        clipboard.on('error', function(e) {
            console.log(e);
        });
    </script>
@endsection
