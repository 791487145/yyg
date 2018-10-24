@extends('layout')
<style>
    .div-a{ float:left;width:49%;text-align: left}
    .div-a span{font-size: 20px;}
    .div-a a{color: #0000FF}
</style>
@section("content")


    <div id="tab_demo" class="HuiTab">
        {{--未审核列表--}}
        <div class="tabCon">
            <div class="page-container">
                <div class="text-c ztext">

                    <table class="table table-border table-bordered table-bg">
                            <thead><tr class="text-c">
                                <th scope="col">名称：{{$talists->ta_name}}</th>
                                <th scope="col">目前排名：NO.{{$talists->id}}</th>
                                <th scope="col">注册地址：{{$talists->address}}</th>
                            </tr>
                            <tr class="text-c">
                                <th>创建时间：{{$talists->created_at}}</th>
                                <th>手机号码：{{$talists->mobile}}</th>
                            </tr>
                            </thead>
                    </table>

                </div>
                <table class="table table-border table-bordered table-bg">
                    <thead>
                    <tr class="text-c">
                        <th width="40">旅行社名称</th>
                        <th width="">实名信息</th>
                        <th width="150">身份证正反面照</th>
                        <th width="150">提交时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="text-c" id="tr_{{$talists->id}}">
                            <td width="20%">{{$talists->ta_name}}</td>
                            <td width="20%">
                                {{$talists->opt_name}}</br>
                                身份证号:{{$talists->opt_id_card}}
                            </td>
                            <td width="20%" class="photos">
                                @if($talists->opt_photo_1)
                                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$talists->opt_photo_1}}"  width="15%">
                                @endif
                                @if($talists->opt_photo_2)
                                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$talists->opt_photo_2}}"  width="15%">
                                    @endif
                            </td>
                            <td width="20%">{{$talists->created_at}}</td>
                            <td width="20%">
                                @if($talists->state == 2)
                                <div><a href="javascript:;" onclick="post_log({{$talists->id}},1)" style="color: #0000FF;">审核通过</a></div>
                                <div><a href="javascript:;" onclick="pass_log('驳回原因','/ta/checkRefuse/{{$talists->id}}')" style="color:red ">审核不通过</a></div>
                                @else
                                    {{$talists->RET}}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>
        $(function(){
            $.Huitab("#tab_demo .tabBar span","#tab_demo .tabCon","current","click","0")});
    </script>

<script type="text/javascript">
    function post_log(id,active){
        layer.confirm('确认通过审核吗？',function(index) {
        $.ajax({
            type:'POST',
            url:'/ta/checkPass',
            datatype:'json',
            data: {'active':active,'id':id},
            success:function(data){
                layer.msg('已审核!',{shift: -1}, function(){
                    window.location.reload();
                });
            }
        });
        });
    }

    function pass_log(title,url){
        layer_show(title,url);
    }

    layer.photos({
        photos: '.photos'
        ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
    });

</script>
@endsection