
@extends('layout')
<style>
    .div-a{ float:left;width:49%;text-align: left}
    .div-a span{font-size: 20px;}
    .div-a a{color: #0000FF}
</style>
@section("content")
<div class="page-container ml-20 mr-20 mt-20">
    <div class="text-c ztext">
            <table class="table table-border table-bordered table-bg">
                <thead><tr class="text-c">
                    <th scope="col">旅行社名称：{{$talist->ta_name}}</th>
                    <th scope="col">手机号码：{{$talist->mobile}}</th>
                    <th scope="col">目前排名：NO.{{$talist->id}}</th>

                </tr>
                <tr class="text-c">
                    <th scope="col">注册地址：{{$talist->address}}</th>
                    <th scope="col">绑定导游人数（人）：{{$talist->ta_guides_count}}</th>
                    <th scope="col">旅行社累计收益：{{$talist->billingSum}}</th>
                </tr>
                <tr class="text-c">
                    <th scope="col">创建时间：{{$talist->created_at}}</th>
                    <th scope="col"></th>
                    <th scope="col"><a style="color: red;" href="/ta/unaudited/{{$talist->id}}">查看审核状态列表</a></th>

                </tr>
                </thead>
            </table>


        <div class="search mt-20 ml-20" style="text-align:left">
            <form>
                导游姓名：<input type="text" class="input-text" style="width:260px;" name="real_name" value="{{$real_name}}" placeholder="请输入关键字搜索">
                &nbsp;&nbsp;手机号码：<input type="text" class="input-text" style="width:260px;" name="mobile" value="{{$mobile}}" placeholder="请输入关键字搜索">
                <button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont"></i> 搜索</button>
                <a class="btn btn-success" href='/ta/guides/export/{{$talist->id}}?real_name={{$real_name}}&mobile={{$mobile}}' onclick="">导出</a>
            </form>

        </div>


    </div>
    <div class="cl pd-5 bg-1 bk-gray mt-20">  <span class="l">共有导游：<strong><?php echo $count;?></strong> 人</span> </div>
    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr>
            <th style="color: red;font-size: 16px" scope="col" colspan="9">导游列表</th>
        </tr>
        <tr class="text-c">
            <th width="40">序号</th>
            <th width="20%">导游姓名</th>
            <th>手机号</th>
            <th>导游绑定游客数</th>
            <th>关注公众号游客数</th>
            <th>导游累计收益</th>
            <th>注册时间</th>
        </tr>
        </thead>
        <tbody>

        @foreach($tabases as $tabase)
        <tr class="text-c">
            <td>{{$tabase->id}}</td>
            <td class="photos">
                @if($tabase->avatar)
                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$tabase->avatar}}"  width="70" >&nbsp;
                @endif

                    @if($tabase->real_name)
                        {{$tabase->real_name}}
                    @else
                        GID.{{$tabase->id}}
                    @endif
            </td>
            <td >{{$tabase->mobile}}</td>
            <td >{{$tabase->count}}</td>
            <td >{{$tabase->count_user_follow_WX}}</td>
            <td >￥{{$tabase->amount}}</td>
            <td >{{$tabase->created_at}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <?php echo $tabases->render();?>
</div>
@endsection

@section('javascript')
<script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
<script type="text/javascript">
    layer.photos({
        photos: '.photos'
        ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
    });
</script>
@endsection