@extends('layout')
<style>

</style>
@section("content")
    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr>
            <form id="form">
            <div style="width: 500px;line-height: 50px">日期:
                <input type="hidden"  value=""  name="state">
                <input type="text" class="Wdate created_at_min" style="width:35%;height: 25px" value="@if(isset($dateStart)) {{$dateStart}} @endif"  name="created_at_min"  id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'})" />到

                <input type="text" class="Wdate created_at_max" style="width:35%;height: 25px" value="@if(isset($dateEnd)) {{$dateEnd}} @endif"   name="created_at_max"  id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'})" />
                <a  class="btn btn-success search"  >查询</a>
                <input type="submit" style="display: none" />
            </div>
            </form>
        </tr>
        @if(strtotime($yestorday) == strtotime($dateStart))
            <tr class="text-c">
                <th>昨日成交额</th>
                <th>昨日成交单数</th>
                <th>累计营业额</th>
            </tr>
        @else
            <tr class="text-c">
                <th>指定日期成交额</th>
                <th>指定日期成交单数</th>
                <th>累计营业额</th>
            </tr>
        @endif
            <tr class="text-c">
                <th  scope="col">￥{{$search_amount}}</th>
                <th  scope="col">{{$search_order_count}}</th>
                <th  scope="col">￥{{$amount}}</th>
            </tr>

        </thead>
    </table>
    <table class="table table-border table-bordered table-bg">
        <tbody>
        <tr class="text-c">
            <th></th>
            <th>江西馆王宇川</th>
            <th>张家界金兴明</th>
            <th>广西万卫</th>
            <th>杭州周涛</th>
            <th>乡亲直供馆</th>
            <th>新麦点张世新</th>
        </tr>
        <tr class="text-c">
            @if($yestorday == $dateStart)
                <td>昨日成交笔数</td>
            @else
                <td>指定日期成交笔数</td>
            @endif
            @foreach($pavilion_yestorday_order_count as $v)
                <td>{{$v}}</td>
            @endforeach

        </tr>
        <tr class="text-c">
            @if($yestorday == $dateStart)
                <td>昨日成交额</td>
            @else
                <td>指定日期成交额</td>
            @endif
            @foreach($pavilion_yestorday_amount as $v)
                <td>￥{{$v}}</td>
            @endforeach
        </tr>
        <tr class="text-c">
            <td>累计营业额</td>
            @foreach($pavilion_amount as $v)
                <td>￥{{$v}}</td>
            @endforeach

        </tr>
        </tbody>
    </table>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(".search").click(function(){
            debugger
            var min = $(".created_at_min").val();
            var max = $(".created_at_max").val();
            if(min != ''){
                if(max == ''){
                    layer.msg('时间必须填写');
                }else{
                    $("input[type=submit]").click();
                }
            }else{
                if(max == ''){
                    $("input[type=submit]").click();
                }else{
                    layer.msg('时间必须填写');
                }
            }
        })

    </script>
@endsection