<style>
    .search{backgound:url({{asset('images/search_w.png')}}) no-repeat right center #a8a7a7;cursor: pointer;    padding: 0 40px 0 0px !important;}
    .search input{padding-left:15px;}
    .search-btn{display:block;width: 40px;height: 28px;position: absolute;right: 0;top:6px;backgound:url({{asset('images/search_w.png')}}) no-repeat;z-index: 999;}
</style>
@extends('supplier')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <div class="statusTab">
                <span @if($state == 0) class="active" @endif><a href="{{url('/goods/review/0')}}">待审核</a></span>
                <span @if($state == 1) class="active" @endif><a href="{{url('/goods/review/1')}}">已通过</a></span>
                <span @if($state == 3) class="active" @endif><a href="{{url('/goods/review/3')}}">已驳回</a></span>
                <div class="search">
                    <form id="search">
                        <input type="text" placeholder="请输入关键字搜索" name="keywords" value="{{ isset($goodsBase->keywords) ? $goodsBase->keywords : '' }}"/>
                        <i class="search-btn"></i>
                    </form>
                </div>
            </div>
            <div class="tabBox">
                <div class="active">
                    <table class="goodsTable">
                        <tr>
                            <th>商品ID</th>
                            <th>商品信息</th>
                            <th>所属分馆</th>
                            <th>所属分类</th>
                            <th>提交时间</th>
                            <th>审核状态</th>
                            <th>操作</th>
                        </tr>
                        @forelse($goodsBase as $goods)
                            <tr>
                                <td>{{$goods->id}}</td>
                                <td><dl>
                                        <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{isset($goods->img)?$goods->img:''}}"></dt>
                                        <dd><p class="limitText">{{$goods->title}}</p><p>供货价：{{$goods->price['price_buying']}}</p></dd>
                                    </dl></td>
                                <td>{{$goods->pavilion_name}}</td>
                                <td>{{$goods->category_name}}</td>
                                <td>{{$goods->updated_at}}</td>
                                <td>{{$goods->status}}</td>
                                <td>
                                    @if($goods->state <= 1)
                                        <a href="/goods/{{$goods->id}}/show">查看</a>
                                    @else
                                        <a href="/goods/{{$goods->id}}/review">编辑</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                        @endforelse

                    </table>
                    <div class="footPage">
                        <p>共{{$goodsBase->lastPage()}}页,{{$goodsBase->total()}}条数据 ；每页显示{{$goodsBase->perPage()}}条数据</p>
                        <div class="pageLink">
                            {!! $goodsBase->appends(['keywords'=>isset($goodsBase->keywords)?$goodsBase->keywords:''])->render() !!}
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    <script type="text/javascript">
        $(function () {
            $('.search-btn').click(function(){
                $('#search').submit();
            });
        })

    </script>
@stop