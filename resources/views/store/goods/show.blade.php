@extends('supplier')
@section('content')
    <style>
        .uploadify-queue{display: none;}
        .tab-del{display: block;width: 19px;height: 19px;position: relative;left: 810px;background: url("{{asset('images/icon_del.png')}}");cursor: pointer; }
        .img-del{float: left;background: url("{{asset('images/acrossTab-close.png')}}") -91px -13px;cursor: pointer;width: 13px;height: 13px;display: inline-block;position: relative;left: -12px;}
        .goodsTable{width:800px !important;}
        .limitText{width: 250px;}
    </style>
    <link rel="stylesheet" href="{{asset('lib/imgbox/css/lrtk.css')}}" />
    <script type="text/javascript" src="{{asset('lib/imgbox/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/imgbox/js/jquery.imgbox.pack.js')}}"></script>
    <div class="rightCon">
        <div class="wrap">
            <form class="form" action="{{url('/goods',$goods->id)}}" method="post">
                <input type="hidden" name="_method" value="put" />
                <div class="box">
                    <h4>发布商品</h4>
                    <table>
                        <tr>
                            <th width="114"><b class="noempty">*</b>商品品类：</th>
                            <td width="400">
                                    {{\App\Models\ConfCategory::getName($goods->category_id)}}
                            </td>

                        </tr>
                        <tr>
                            <th width="114"><b class="noempty">*</b>商品名称：</th>
                            <td width="400"><span>{{$goods->title}}</span>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>所属分馆：</th>
                            <td>
                                {{\App\Models\ConfPavilion::getName($goods->pavilion)}}
                            </td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>封面图：</th>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="fileResult oneFileResult">
                                    <span class="cover">
                                        <a href="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$goods->cover}}" class="goods-cover-imgbox">
                                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$goods->cover}}" alt="" width="220" height="100">
                                        </a>
                                    </span>

                                </div>
                                <p class="tip">（建议尺寸：700*320，封面图将用于商品列表等~）</p>
                            </td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>轮播图：</th>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="fileResult oneFileResult">
                                    <span></span>
                                    @if($goods->images)
                                        @forelse($goods->images as $image)
                                            <span class="images">
                                                <a href="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$image->name}}" class="goods-imgbox">
                                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$image->name}}" alt="" width="100" height="100">
                                                </a>
                                            </span>
                                        @empty
                                        @endforelse
                                    @endif

                                </div>
                                <p class="tip">（建议尺寸：800*800，请添加5~9张图片）</p>
                            </td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>重要提示：</th>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="textCount">{{$goods->ext->important_tips}}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <h4>商品信息</h4>
                    @foreach($goods->spec as $key=>$spec)
                        <table class="rightTh">
                            <tr>
                                <th><b class="noempty">*</b>规格：</th>
                                <td width="400">{{$spec->name}}</td>
                                <th>包装件数：</th>
                                <td width="400">{{$spec->pack_num}}</td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>重量：</th>
                                <td width="400">{{$spec->weight}}克</td>
                                <th><b class="noempty">*</b>净含量：</th>
                                <td width="400">{{$spec->weight_net}}克</td>
                            </tr>
                            <tr>
                                <th>长度：</th>
                                <td width="400">{{$spec->long}}M</td>
                                <th>宽度：</th>
                                <td width="400">{{$spec->wide}}M</td>
                            </tr>
                            <tr>
                                <th>高度：</th>
                                <td width="400">{{$spec->height}} M</td>
                                <th><b class="noempty">*</b>供应价：</th>
                                <td width="400">{{$spec->price_buying}}元</td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>库存：</th>
                                <td width="400">{{$spec->num}}件<span class="stock">已售出：<i>{{$spec->num_sold}}</i>件</span></td>
                                <th>建议零售价：</th>
                                <td width="400">{{$spec->price}}元</td>
                            </tr>
                            <tr>
                                <th><b class="noempty">*</b>原价：</th>
                                <td width="400">{{$spec->price_market}}元</td>
                            </tr>
                        </table>
                    @endforeach
                </div>
                    @if($param['state'] == 0 && $param['is_pick_up'] == 0)
                    @else
                    <div class="box">
                        <h4>邮费设置</h4>
                        <table class="rightTh">
                            <tr>
                                @if($param['state'] == 1)
                                    <td>
                                        <input type="checkbox" disabled checked  class="express_fee_mode">满{{$param['total_amount']}}元包邮，未满另加{{$param['express_amount']}}元
                                    </td>
                                @endif
                                @if($param['is_pick_up'] == 1)
                                    <td>
                                        <input type="checkbox" disabled checked class="is_pick_up">自提</label>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </div>
                    @endif
                <div class="box">
                    <h4>基本属性</h4>
                    <table class="rightTh">
                        <tr>
                            <th><b class="noempty">*</b>发货地：</th>
                            <td width="400">{{$goods->ext->send_out_address}}</td>
                            <th><b class="noempty">*</b>产地：</th>
                            <td width="400">{{$goods->ext->product_area}}</td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>保质期：</th>
                            <td width="400">{{$goods->ext->shelf_life}}</td>
                            <th><b class="noempty">*</b>贮藏：</th>
                            <td width="400">{{$goods->ext->store}}</td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>包装：</th>
                            <td width="400">{{$goods->ext->pack}}</td>
                            <th><b class="noempty">*</b>快递说明：</th>
                            <td width="400">{{$goods->ext->express_desc}}</td>
                        </tr>
                        <tr>
                            <th><b class="noempty">*</b>发货说明：</th>
                            <td width="400">{{$goods->ext->send_out_desc}}</td>
                            <th><b class="noempty">*</b>售后说明：</th>
                            <td width="400">{{$goods->ext->sold_desc}}</td>
                        </tr>
                        <tr>
                            <th>食品添加剂：</th>
                            <td width="400">{{$goods->ext->food_addiitive}}</td>
                            <th>生产许可证：</th>
                            <td width="400">{{$goods->ext->product_license}}</td>
                        </tr>
                        <tr>
                            <th>等级：</th>
                            <td width="400">{{$goods->ext->level}}</td>
                            <th>制造厂商/公司：</th>
                            <td width="400">{{$goods->ext->company}}</td>
                        </tr>
                        <tr>
                            <th rowspan="3">配料表：</th>
                            <td width="400" rowspan="3">{{$goods->ext->food_burden}}</td>
                            <th>经销商：</th>
                            <td width="400">{{$goods->ext->dealer}}</td>
                        </tr>
                        <tr>
                            <th>地址：</th>
                            <td width="400">{{$goods->ext->address}}</td>
                        </tr>
                        <tr>
                            <th>特别说明：</th>
                            <td width="400">{{$goods->ext->remark}}</td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <h4>赠品信息</h4>

                        @forelse($goods->gift as $gift)
                            <table class="goodsTable presentTable">
                                <tbody>
                                <tr>
                                    <td width="50">{{isset($gift->goods->id)?$gift->goods->id:''}}</td>
                                    <td width="350">
                                        <dl>
                                            <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{isset($gift->goods->cover)?$gift->goods->cover:''}}"></dt>
                                            <dd><p class="limitText">{{isset($gift->goods->title)?$gift->goods->title:''}}</p>
                                                <p>供货价：￥{{isset($gift->spec->price)?$gift->spec->price:''}}</p></dd>
                                        </dl>
                                    </td>
                                    <td width="100">规格：{{isset($gift->spec->name)?$gift->spec->name:''}}</td>
                                    <td width="100">库存{{isset($gift->goods->num)?$gift->goods->num:''}}件</td>
                                    <td width="100">
                                </tr>
                                </tbody>
                            </table>
                        @empty
                            <span>无</span>
                        @endforelse

                </div>
                <div class="box">
                    <h4>商品详情</h4>
                    <div>{!! $goods->ext->description !!}</div>
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
                        $(".goods-cover-imgbox").imgbox({
                            'speedIn'		: 0,
                            'speedOut'		: 0,
                            'alignment'		: 'center',
                            'overlayShow'	: true,
                            'allowMultiple'	: false
                        });
                    });
                </script>


@stop

