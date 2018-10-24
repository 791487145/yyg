@extends('layout')
    <style>
        #all{
            margin-left: 1%;
        }
    </style>
@section("content")
    <div id="all">
        <div style="margin-top: 15px">

            <table class="table table-border table-bordered table-hover">
                <tr>
                    <td>供应商姓名：{{$SupplierBase->name}}</td>
                    <td>手机号：{{$SupplierBase->mobile}}</td>
                    <td>保证金{{$SupplierBase->deposit}}&nbsp;&nbsp;&nbsp;<a class="c-blue" href="javascrpt::" onclick="confirm_action('{{$SupplierBase->name}}店长，你好，你在易游购平台上的保证金已不足￥2000元，为了保证您的商品在平台上正常售卖，请尽快联系我们平台工作人员，多有不便，敬请谅解~易游购宣',
                                '/cuscomer/suppliers/{{$SupplierBase->id}}' )">提醒补交保证金</a>
                        {{--发送短信--}}
                    </td>

                </tr>
                <tr>
                    <td>店铺名称：{{$SupplierBase->store_name}}</td>
                    <td>累计销售额：￥{{$SupplierBase->amount}}</td>
                    <td>
                        状态：
                        <span class="select-box" style="width: 60%">
                            <select class="select" size="1" name="state" >
                                <option value="1" {{ ($SupplierBase->state == 1) ? 'selected' :'' }}>正常</option>
                                <option value="-1" {{ ($SupplierBase->state == -1) ? 'selected' :'' }} >禁用</option>
                            </select>
                        </span>
                    </td>
                </tr>
            </table>

        </div>
        <div style="margin-top: 15px">
            <table class="table table-border table-bordered table-hover">
                <tr class="text-c">
                    <td>序号</td>
                    <td>商品名称</td>
                    <td>库存</td>
                    <td>销量</td>
                    <td>销售额</td>
                    <td>所属分馆</td>
                    <td>上架时间</td>
                    <td>操作</td>
                </tr>
                @foreach($GoodsBases as $key => $GoodsBase)
                <tr class="text-c" id="tr_{{$GoodsBase->id}}">
                    <td style="width:5%">
                            {{$GoodsBase->id}}
                    </td>
                    <td style="width:40%">
                        <div style="width:20% ;"  class="f-l photos">
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$GoodsBase->cover}}" layer-pid="{{$key}}" class="round mt-10" width="50" height="50">
                        </div>
                        <div class="f-l" style="width:75%;text-align: left;margin-left: 10px;">
                            {{$GoodsBase->title}}</br>
                            供货价：{{$GoodsBase->prices_buying}}</br>
                            零售价：{{$GoodsBase->prices}}
                        </div>
                    </td>
                    <td>{{$GoodsBase->num}}</td>
                    <td>{{$GoodsBase->num_sold}}</td>
                    <td>{{$GoodsBase->amount}}</td>
                    <td>{{$GoodsBase->pavilion_name}}</td>
                    <td>{{$GoodsBase->created_at}}</td>
                    <td>
                        <a title="查看" href="{{url('goods/edit',$GoodsBase->id)}}" class="ml-5" style="text-decoration:none">
                               <i class="Hui-iconfont">&#xe709;</i>
                        </a>
                    </td>

                </tr>
                    @endforeach

            </table>
        </div>

        <?php echo $GoodsBases->render();     ?>
    </div>
@endsection

@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>
        function a(title,url){
            layer_show(title,url);
        }

        function confirm_action(message,url){
            layer.confirm(message,{btn: ['短信发送', '取消']
                ,yes: function(index){
                    $.get(url,function(data){
                        if(data.ret == 'yes'){
                            layer.msg('已发送!',{icon:1,time:1000});
                        }else{
                            layer.msg('短信发送失败!',{icon:5,time:1000});
                        }
                    })
                    layer.closeAll();
            }});
        }
    </script>
    <script>
        $(function(){
           $('.select').change(function(){
              var state = $(this).children('option:selected').val();
               $.post('/cuscomer/supplier/{{$SupplierBase->id}}',{state:state},function(msg){
                    if(msg.state == 1){
                        layer.msg('已正常!',{icon:1,time:1000});
                    }else {
                        layer.msg('已禁用!',{icon:1,time:1000});
                    }
               })
           })
        })

        layer.photos({
            photos: '.photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });
    </script>
@endsection