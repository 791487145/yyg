@extends('layout')
    <style>
        #all{
            margin-left: 1%;
            margin-top: 1%;
        }
        .goods-nav .active{
            border-bottom: 2px solid #4395ff !important;
        }
        .goods-nav{font-size:14px;margin-bottom:20px;}
        .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
    </style>
@section("content")
    <div id="all">

        <div class="text-r">

            <form >

                <table class="table table-border table-bordered table-hover">
                    <tr>
                        <td>
                            <input type="text" class="btn btn-default radius" placeholder="输入导游姓名" name="real_name" value="{{$tmp['real_name']}}" >
                            <input type="hidden" name="state" value="{{$state}}">
                            <button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="goods-nav">
            <a class="@if($state == \App\Models\UserBase::state_upload_2cert) active @endif" href="/cuscomer/guiders/{{\App\Models\UserBase::state_upload_2cert}}">待审核</a>
            <a class="@if($state == \App\Models\UserBase::state_check) active @endif" href="/cuscomer/guiders/{{\App\Models\UserBase::state_check}}">已通过</a>
            <a class="@if($state == \App\Models\UserBase::state_no_check) active @endif" href="/cuscomer/guiders/{{\App\Models\UserBase::state_no_check}}">已驳回</a>
        </div>
        <div style="margin-top: 15px">
            <table class="table table-border table-bordered table-hover">
                <tr class="text-c">
                    <td>序号</td>
                    <td>导游姓名</td>
                    <td>手机号码</td>
                    <td>导游证卡号/身份证号</td>
                    <td>手持导游证照/身份证照</td>
                    <td>注册时间</td>
                    <td>操作</td>
                </tr>
                @foreach($UserBases as $UserBase)
                <tr class="text-c" id="tr_{{$UserBase->id}}">
                    <td style="width:20%">
                            {{$UserBase->id}}
                    </td>
                    <td>{{$UserBase->real_name}}</td>
                    <td>{{$UserBase->mobile}}</td>
                    <td>{{$UserBase->guide_no}}</td>
                    <td class="photos">
                        @if($UserBase->guide_photo_1 != '')
                        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$UserBase->guide_photo_1}}"  class="radius" width="50px" height="40px">
                            @endif
                        @if($UserBase->guide_photo_2 != '')
                        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$UserBase->guide_photo_2}}"  class="radius" width="50px" height="40px">
                            @endif
                    </td>
                    <td>{{$UserBase->created_at}}</td>
                    @if($UserBase->state == 11)
                    <td>
                      <a title="删除" href="javascript:;" onclick="confirm_action('确认审核通过吗？？','/cuscomer/guider/{{$UserBase->id}}')" class="ml-5" style="text-decoration:none">通过审核</a> |
                        <a href="javascript:;" onclick="a( '审核不通过', '/cuscomer/guider/check/{{$UserBase->id}}')">审核不通过</a>
                    </td>
                    @endif
                    @if($UserBase->state == 1)
                        <td>
                            <span class="c-999">审核通过</span>
                        </td>
                    @endif
                    @if($UserBase->state == 2)
                        <td>
                           <span class="c-999">已驳回</span>
                        </td>
                    @endif
                </tr>
                    @endforeach

            </table>
        </div>
        <span style="float: left;line-height: 70px;margin: 0 20px">共{{$UserBases->lastPage()}}页,{{$UserBases->total()}}条数据 ；每页显示{{$UserBases->perPage()}}条数据</span>
        <?php echo $UserBases->appends(['real_name'=>$tmp['real_name'],'state'=>$state])->render();     ?>
    </div>
@endsection

@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>
        function a(title,url){
            layer_show(title,url,400,300,function(){
                parent.location.replace(parent.location.href);
            });
        }

        function confirm_action(message,url){
            layer.confirm(message,function(index){
                $.get(url,function (data) {
                    $("#tr_"+data).remove();
                    layer.msg('已通过!',{icon:1,time:1000});
                })
            });
        }
    </script>
    <script>

            layer.photos({
                photos: '.photos'
                ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            });



    </script>
@endsection