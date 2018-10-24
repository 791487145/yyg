@extends('supplier')
@section('content')
    <link href="/css/H-ui.min.css" rel="stylesheet" type="text/css" />
    <link href="/css/H-ui.admin.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{asset('/css/bootstrapSwitch.css')}}">
    <style>
        .setDiv{position: fixed;background: rgba(0,0,0,0.5);width: 100%;height: 100%;z-index: 999;
            left:0;right: 0;top: 0;display: none;}
        .form{position: absolute;left: 50%;top: 50%;transform: translate(-50%, -50%);background: #fff;}
        .form h4{font-size:18px;background: #E3E3E3;padding: 10px 20px;margin-top: 0px;}
        .form .box{padding: 20px;width:320px;}
        .form select{width:100px;padding: 4px;}
        .Preservation{padding: 8px 20px;background:#e7641c;color: #FFF;display:block;margin: 20px auto;border: 0;cursor: pointer;}
        .inputText{width: 184px; height: 30px;border: 1px solid #6e6e6e;padding: 0 8px;margin-right: 14px;}
        .form .fileUpload {position: relative;display: inline-block;width: 100px;height: 100px;background: url(../images/file_icon_03.png);float: left;background-size: 100px;}
        .deletePopup{float: right;font-size: 16px;cursor: pointer;}
        .inputText{border: 1px solid #e0e0e0;padding:0px 6px;width: 50px;margin: 0 4px;}
    </style>
<div class="rightCon">
    <div class="wrap">
        <h2><span>邮费设置</span></h2>
        <input type="hidden" value="{{(isset($express->id)) ? $express->id : 0}} " id="express_id">
            <div class="box">
                <div>
                    {{$express->title}}      条件：满{{$express->total_amount}}包邮，未满另加邮费{{$express->express_amount}}元
                    <span class="add" style="float: right;color: blue;cursor: pointer">编辑&nbsp;</span>
                </div>
                <hr style="border:0px;height: 1px;background: #e0e0e0;margin: 20px 0;"/>
                <div>
                    允许自提
                    <div style="float: right" class="switch free" data-on-label="开" data-off-label="关" data-on="primary" data-off="danger" data-animated="false" >
                        <input type="checkbox"  @if($supplierBase->is_pick_up == 1) checked @endif  name="state"/>
                    </div>
                </div>
            </div>
    </div>
</div>
    {{--自提--}}
    <div class="setDiv everything">
        <form class="form">
            <h4>&nbsp; <span class="deletePopup">x</span></h4>
            <div class="box" style="width: 500px;">
                <div>启动该条件后，全店商品都会使用该邮费条件，请须知！</div>
                <div style="margin: 20px auto;width: 150px">
                    <input type="button" class="btn btn-warning radius pick_sure"value="确定"/>&nbsp;&nbsp;&nbsp;
                    <input type="button" class="btn btn-default radius deletePopup_add"value="取消"/>
                </div>
            </div>
        </form>
    </div>
    <div class="setDiv addArriage">
        <form class="form" method="post" action="/supplierExpress" id="form">
            <h4>请填写供应商信息 <span class="deletePopup">x</span></h4>
            <input type="hidden" value="{{$supplierBase->id}}" name="supplier_id">
            <input type="hidden" @if(!empty($express)) value="{{$express->id}}" @endif name="id">
            <div class="box" style="width: 500px;">
                <div style="margin-bottom: 20px;">活动名称：<input type="text" @if(!empty($express)) value="{{$express->title}}" @endif class="inputText" datatype="*" style="width: 150px;" name="title"></div>
                <div>条件：全店满<input type="num" class="inputText" @if(!empty($express)) value="{{$express->total_amount}}" @endif datatype="*" name="total_amount">包邮，未满另加邮费<input type="text" class="inputText" @if(!empty($express)) value="{{$express->express_amount}}" @endif datatype="*" name="express_amount"> 元，</div>
                <div style="margin: 20px auto;width: 150px">
                    <input type="submit" class="btn btn-warning radius"value="确定"/>&nbsp;&nbsp;&nbsp;
                    <input type="button" class="btn btn-default radius deletePopups"value="取消"/>
                </div>
                <p>注：全国包邮设置：满0包邮，未满加0元。</p>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="{{asset('/js/bootstrapSwitch.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/layer/2.1/layer.js')}}"></script>
    <script type="text/javascript">
        function hide(){
            $(".nothing,.everything,.addArriage,.everythings").hide();
        }
        //自提取消锁定
        $('.free').on('switch-change', function (e, data) {
            if (data.value) {
                $(".everything").show();
            }else{
                $.post("/supplierExpress/edit",{state:0,action:'is_pick_up'},function(data){
                    if(data.ret == "no"){
                        layer.msg('修改失败',{icon:1,time:1000});
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                    if(data == "yes"){
                        layer.msg('已修改!',{icon:1,time:1000});
                    }
                })
            }
        });
        $(".add").click(function(){
            hide();
            $(".addArriage").show();
        })

        $(".deletePopup").click(function(){
            hide();
        });
        //
        $(".deletePopups").click(function(){
            hide();
        });

        $(".deletePopup_add").click(function(){
            $(".free .switch-on").addClass("switch-off").removeClass("switch-on");
            hide();
        });

        //自提确定
        $(".pick_sure").click(function(data){
            $.post("/supplierExpress/edit",{state:1,action:'is_pick_up'},function(data){
                $(".free .switch-on").addClass("switch-on").removeClass("switch-off");
                hide();
            })
        })

        $("#form").Validform({
             tiptype:2,
             ajaxPost:true,
             postonce:true,
             callback:function(data){
                layer.msg(data.content,{icon:1,time:1000});
                 location.reload();
             }
         });
    </script>
    @stop