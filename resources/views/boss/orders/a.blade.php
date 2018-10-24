<div id="all" style="line-height: 30px">
    <div class="panel-header">订单详情</div>
    <div class="panel-body">
        <div>
            <label>订单信息</label>
            <div>
                <table class="table table-border table-bordered table-hover">
                    <tr class="">
                        <td>订单编号：{{$orders->order_no}}</td>
                        <td>订单金额：{{$orders->amount_goods}}</td>
                    </tr>
                    @foreach($orders->tmp as $good)
                        <tr class="success">
                            <td>商品名称：{{$good['title']}}</td>
                            <td>实付：{{$good['price']}}</td>
                        </tr>
                        <tr class="">
                            <td>规格：{{$good['packname']}}</td>
                            <td>数量：{{$good['num']}}</td>
                        </tr>
                        <tr class="">
                            <td>零售价：{{$good['price']}}</td>
                            <td>建议零售价:{{$good['price']}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <br />
        <div> 供应商名称: {{$orders->supplier_name}} </div>
        <div> 供应商号码: {{$orders->supplier_mobile}} </div>
        <div>付款方式：
            @if($orders->pay_type ==\App\Models\OrderBase::PAY_TYPE_ALI)
                ping++支付宝支付
            @endif
            @if($orders->pay_type ==\App\Models\OrderBase::PAY_TYPE_WX)
                ping++微信支付
            @endif
            @if($orders->pay_type ==\App\Models\OrderBase::Pay_TYPE_WX_JS)
                微信商户支付
            @endif
        </div><div>付款时间：{{$orders->created_at}}</div>
        <div> 配送方式: {{$orders->express_type}}</div>
        <div> 运费: {{$orders->amount_express}}</div>
        <div><label>收货人信息 </label>：{{$orders->receiver_info['name']}} {{$orders->receiver_info['mobile']}}{{$orders->receiver_info['province']}}{{$orders->receiver_info['city']}}{{$orders->receiver_info['district']}} {{$orders->receiver_info['address']}}</div>
        <div><label>物流公司</label>：{{$orders->express_name}}</div>
        <div>
            <label>物流单号</label>：{{$orders->express_no}}
            <a class="btn btn-success" href="http://www.baidu.com/s?wd={{$orders->express_name}}+{{$orders->express_no}}">手动查询物流信息</a>
        </div>
        <div><label>买家留言： </label>{{$orders->buyer_message}}</div>

        <div class="line"></div>
        @if($orders->state != \App\Models\OrderReturn::STATE_REFUSE)
            <div><label>售后信息： </label></div>
            <div><label>状&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;态： </label><span style="font-size: 20px">{{$orderState}}</span>
            <span style="margin-left: 200px;"><label>提交时间：</label>{{$orders->updated_at}}</span></div>
            <div>
                <div>售后原因：{{$orders->return_content}}</div>
                <div class="photos">
                    @foreach($orders->returnImg as $images)
                        <img layer-src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$images['name']}}" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$images['name']}}" width="90px" height="90px" alt="">
                    @endforeach
                </div>
                @if($orders->state == \App\Models\OrderReturn::STATE_NO_REFUND)
                    <div>退款说明：{{$orders->return_info}}</div>
                    <div>退款金额：{{$orders->amount}}
                        @if($authority == 1)
                            <a class="btn btn-success" href="javascript:;" onclick="pay('/orders/autoRefund/{{$orders->order_no}}','{{$orders->amount}}')">直接退款</a></div>
                        @endif
                    </div>
                @endif
                @if($orders->state == \App\Models\OrderReturn::STATE_SUCCESS)
                    <div>退款说明：{{$orders->return_info}}</div>
                    <div>退款金额：{{$orders->amount}}</div>
                @endif
                @if($orders->state == \App\Models\OrderReturn::STATE_REFUSE)
                    <div>驳回原因：{{$orders->return_info}}</div>
                @endif
            </div>
        @endif
    </div>


    <!--售后记录 begain-->
    @if(!empty($refundAuditRecord))
        <p style="font-size: 20px">历史售后记录：</p>
        @foreach($refundAuditRecord as $v)
            <div class="line"></div>
            <div class="panel-body">
                <label>状&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;态： </label>已驳回
                <span style="margin-left: 200px;"><label >提交时间：</label>{{$v->created_at}}</span>

                <div class="margin-top10">售后原因：{{$v->return_content}}</div>
                <div class="photos">
                    @foreach($v->images as $image)
                        <img layer-src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$image['name']}}" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$image['name']}}" width="90px" height="90px" alt="">
                    @endforeach
                </div>

                <div>驳回原因：{{$v->refuse_content}}</div>
                <div>
                    <label>审核时间： </label>{{$v->updated_at}}
                    <span style="margin-left: 200px;"><label >操作人：</label>{{$v->auditer}}</span>
                </div>
            </div>
            <div class="line"></div>
        @endforeach
    @endif
    <!--售后记录 end-->



    @if($orders->state == 0)
        <div style="text-align: center">
            <a href="/orders/check/passing/{{$orders->order_no}}/{{$orders->amount}}" class="btn btn-success">通过审核</a>
            <a href="/orders/check/{{$orders->order_no}}/refuse" class="btn  btn-danger">驳回申请</a>
        </div>
    @endif
    @if($orders->state == 1)
        <div style="text-align: center">
           {{-- @if($authority == 1)
                <a href="javascript:;" onclick="pass('确定要打款吗？','退款金额￥{{$orders->amount}}，退款后资金将原路返回支付账户中','/orders/check/passOrder/{{$orders->order_no}}/{{$orders->amount}}')" class="btn btn-success">手工打款</a>
            @endif--}}
            <a href="javascript:;" onclick="layer_close()" class="btn  btn-danger">返回</a>
        </div>
    @endif
</div>
@section('javascript')
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>
        function pass(title,message,url,text) {
            layer.confirm(title,{
                title:title,
                content:message,
                btn: ['确认', '取消'] //按钮
            }, function () {
                $.get(url,function (data) {
                    parent.location.replace(parent.location.href);
                })
            });
        }

        function pay(url,amount){
            layer.confirm('确定要打款吗？退款金额￥{{$orders->amount}}',function(){
                $.post(url,{amount:amount},function(data){
                    if(data.ret == 'ali_pending'){
                        layer.alert('点击确定将跳转到支付宝退款页面。',function(){
                            window.open(data.msg);
                            parent.location.replace(parent.location.href);
                        });
                    }else{
                        layer.alert(data.msg,function(){
                            parent.location.replace(parent.location.href);
                        });
                    }
                });
            });

        }

        layer.photos({
            photos: '.photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });

    </script>
@endsection