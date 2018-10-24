
@extends('layout')
@section("content")
    <link href="/css/H-ui.min.css" rel="stylesheet" type="text/css" />
    <link href="/css/H-ui.admin.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="http://devstore.yyougo.com/css/bootstrapSwitch.css">
<div class="page-container ml-20 mr-20">
    <div class="text-c">
        <div style="height: 15px;"></div>
        <form method="get" action="/ta/tamanages">
            <span class="inline">旅行社名称<input type="text" class="input-text margin-left10 margin-right10" style="width:250px" placeholder="旅行社名称" value="<?php echo  $ta_name;?>" id="" name="ta_name"></span>
            <span class="inline">手机号码<input type="text" class="input-text margin-left10 margin-right10" style="width:250px" placeholder="手机号码" value="<?php echo  $mobile;?>" id="" name="mobile"></span>
            <span class="inline">注册地
                <span class="select-box margin-left10 margin-right10" style="width: 15%">
                <select class="select" size="1" name="pavilion_id">
                    <option value="">
                        <?php
                            foreach($provincename as $v){
                               echo !empty($v)?$v:'请选择';
                            }
                        ?>
                    </option>
                        @foreach($confcitys as $key => $v)
                            <option value="{{$key}}">{{$v}}</option>
                        @endforeach
                    </span>
                </select>
            </span>
            <span><input type="hidden" value="1" name="select" ></span>
            <span class="inline">日期范围
                <input type="text" name="datemin" onfocus="WdatePicker({ dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}' })" value="<?php echo  $datemin;?>" id="datemin" class="input-text Wdate" style="width:180px;">
            -
            <input type="text" name="datemax" onfocus="WdatePicker({ dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d' })" value="<?php echo  $datemax;?>" id="datemax" class="input-text Wdate" style="width:180px;"></span>
            <input type="submit" class="btn btn-success" id="" value="搜索" name="">
        </form>
        <span><a class="btn btn-success" href="javascript:" onclick="add_ta('添加旅行社','/ta/add/')"><i class="Hui-iconfont">&#xe600;</i>添加旅行社</a></span>
        <span><a class="btn btn-success" href="/ta/export?ta_name={{$ta_name}}&mobile={{$mobile}}&pavilion_id={{$pavilion_id}}&datemin={{$datemin}}&datemax={{$datemax}}" onclick="">导出</a></span>
    </div>
    <div class="cl pd-5 bg-1 bk-gray mt-20">  <span class="l">共有数据：<strong>{{$tabases->total()}}</strong> 条</span> </div>
    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr>
            <th style="color: red;font-size: 16px" scope="col" colspan="11">旅行社列表</th>
        </tr>
        <tr class="text-c">
            <th width="40">序号</th>
            <th width="">名称</th>
            <th width="130">手机号码</th>
            <th width="90">旅行社绑定导游人数</th>
            {{--<th width="150">导入游客数</th>--}}
            <th width="150">旅行社累计收益</th>
            <th width="150">旅行社累计销售额</th>
            <th width="150">本月旅行社累计销售额</th>
            <th width="150">注册地</th>
            <th>注册时间</th>
            <th>状态</th>
            <th width="200">操作</th>
        </tr>
        </thead>
        <tbody>

        @foreach($tabases as $tabase)
        <tr class="text-c">
            <td>{{$tabase->id}}</td>
            <td>{{$tabase->ta_name}}</td>
            <td>{{$tabase->mobile}}</td>
            <td>{{$tabase->guide_count}}</td>
           {{-- <td>{{$tabase->user_count}}</td>--}}
            <td>{{$tabase->amount}}</td>

            <td>{{$tabase->taTurnover}}</td>
            <td>{{$tabase->currentMonthTurnover}}</td>


            <td class="td-status">{{$tabase->address}}</td>
            <td>{{$tabase->created_at}}</td>


            <td>
                <div style="min-width: 60px" class="switch free" data-id="{{$tabase->id}}" data-on-label="开" data-off-label="关" data-on="primary" data-off="danger" data-animated="false" >
                    <input type="checkbox" @if($tabase->state == 1) checked @endif  name="state"/>
                </div>
            </td>


            <td class="td-manage">
                <a style="text-decoration:none;color: red;" href="/ta/tamanage/{{$tabase->id}}" title="查看详细">查看详细</a>
               {{-- <a title="删除" href="javascript:;" onclick="admin_del(this,'1')" class="ml-5" style="text-decoration:none"><i></i></a>
                实名已通过--}}
            </td>

        </tr>
        @endforeach
        </tbody>
    </table>
    <span style="float: left;line-height: 70px;margin: 0 20px">共{{$tabases->lastPage()}}页,{{$tabases->total()}}条数据 ；每页显示{{$tabases->perPage()}}条数据</span>
    <?php echo $tabases->appends(['ta_name'=>$ta_name,'mobile'=>$mobile,'pavilion_id'=>'','datemin'=>$datemin,'datemax'=>$datemax])->render();     ?>

</div>
@endsection
@section('javascript')
<script type="text/javascript" src="http://devstore.yyougo.com/js/bootstrapSwitch.js"></script>

<script type="text/javascript">
    function add_ta(title,url){
        layer_show(title,url);
    }
    //自提取消锁定
    $('.free').on('switch-change', function (e, data) {
        var id = $(this).attr("data-id");
        if (data.value) {
            $.post('/ta/state/'+id+'/1',function(){

            });
        }else{
            $.post('/ta/state/'+id+'/4',function(){

            });
        }
    });
</script>
@endsection