@extends('travel')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>查看详情</span></h2>
            <div class="box guideDetail">
                <dl>
                    <dt>旅行团名称：</dt>
                    <dd>{{$group->title}}</dd>
                </dl>
                <dl>
                    <dt>指派导游：</dt>
                    <dd>{{$group->guide_name}}   {{$group->guide_mobile}}</dd>
                </dl>
                <dl>
                    <dt>状态：</dt>
                    <dd>{{\App\Models\TaGroup::getStateCN($group->state)}}</dd>
                </dl>
                <dl>
                    <dt>该团绑定游客数（人）：</dt>
                    <dd>{{$group->num}}</dd>
                </dl>
                <dl>
                    <dt>该团销量（笔）：</dt>
                    <dd>{{$group->groupSaleNum}}</dd>
                </dl>
                <dl>
                    <dt>该团销售额（元）：</dt>
                    <dd>{{$group->groupSalesAmount}}</dd>
                </dl>
                
                <dl>
                    <dt>开始时间：</dt>
                    <dd>{{$group->start_time}}</dd>
                </dl>
                <dl>
                    <dt>结束时间：</dt>
                    <dd>{{$group->end_time}}</dd>
                </dl>
            </div>
            <h6>该时间段成交明细</h6>
            <table class="detailTable">
                <tr>
                    <th>序号</th>
                    <th>订单编号</th>
                    <th>商品名称</th>
                    <th>购买数量</th>
                    <th>订单总价</th>
                    <th>下单时间</th>
                    <th>旅行社获利</th>
                    <th>订单状态</th>
                </tr>
                @forelse($orderBaseInfo as $order)
                    <tr>
                        <td>{{$order->id}}</td>
                        <td>{{$order->order_no}}</td>
                        <td>{{$order->goodInfo->goods_title}}</td>
                        <td>{{$order->goodInfo->num}}</td>
                        <td>￥{{$order->amount_goods}}</td>
                        <td>{{$order->created_at}}</td>
                        <td>
                            @if(!empty($order->rebateinfo))
                                {{$order->rebateinfo->amount - $order->rebateinfo->return_amount}}
                            @else
                                0
                            @endif
                        </td>
                        <td>
                            @if($order->state == 1)
                                                                                            待发货
                            @elseif($order->state == 2)  
                                                                                            待收货
                            @else
                                                                                            已完成
                            @endif                                                       
                        </td>
                    </tr>
                @empty
                @endforelse

            </table>
            <div class="footButton">
                <input type="button" value="返回" onclick="javascript:history.back(-1);">
            </div>
            <div class="footPage">
                <p>共{{$orderBaseInfo->lastPage()}}页,{{$orderBaseInfo->total()}}条数据 ；每页显示{{$orderBaseInfo->perPage()}}条数据</p>
                <div class="pageLink">
                    {!! $orderBaseInfo->render() !!}
                </div>
            </div>
        </div>
    </div>
    </div>
@stop