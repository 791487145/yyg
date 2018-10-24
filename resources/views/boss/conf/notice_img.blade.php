@extends('layout')
<style>
    #all{
        margin-left: 1%;
    }
    .goods-nav .active {
        border-bottom: 2px solid #4395ff !important;
    }
    .goods-nav a {
        display: inline-block;
        margin-right: 20px;
        padding: 5px;
        text-decoration-line: none !important;
    }
</style>
@section("content")
    <div class="goods-nav">
        <a @if($state == 1)class="active " @endif href="/conf/confnotices">平台公告</a>
        <a @if($state == 2)class="active" @endif href="/conf/confImgnotices">图文公告</a>
    </div>
<div class="page-container">
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l"><a class="btn btn-primary radius" onclick="altpage('添加图片','/conf/confImgnotice')" href="javascript:;"><i class="Hui-iconfont"></i> 添加图文</a></span>
    </div>
    <div class="mt-20">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper no-footer"><table class="table table-border table-bordered table-bg table-hover table-sort dataTable no-footer" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
                <thead>
                <tr class="text-c" role="row">
                    <th width="100" class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"  style="width: 210px;">标题/封面</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"  style="width: 637px;">摘要</th>
                    <th width="150" class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"  style="width: 150px;">url</th>
                    <th width="150" class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"  style="width: 150px;">创建时间</th>
                    <th width="60" class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"  style="width: 60px;">发送时间</th>
                    <th width="100" class="sorting_disabled" rowspan="1" colspan="1" aria-label="操作" style="width: 100px;">操作</th>
                </tr>
                </thead>
                <tbody>

                @foreach($platFormNotices as $platFormNotice)
                <tr class="text-c odd" role="row">
                    <td><h4>{{$platFormNotice->title}}</h4><img width="210" class="picture-thumb" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$platFormNotice->cover}}"></td>
                    <td class="text-l">{{$platFormNotice->content}}</td>
                    <td class="text-c">{{$platFormNotice->url}}</td>
                    <td>{{$platFormNotice->created_at}}</td>
                    <td class="td-status">{{$platFormNotice->updated_at}}</td>
                    <td class="td-manage">

                        <a title="{{($platFormNotice->state==1)? '已发送':'未发送'}}" href="javascript:;" onclick="a( '发送', '/conf/confnotices/{{$platFormNotice->id}}')" class="ml-5" style="text-decoration:none">
                            {{($platFormNotice->state == 1)?'已发送':'发送'}}
                        </a>
                        <a title="删除" href="javascript:;" onclick="confirm_action('删除','/conf/confnotice/{{$platFormNotice->id}}')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6e2;</i>
                        </a>

                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <?php echo $platFormNotices->render();    ?>
        </div>
    </div>
</div>

@endsection
<script type="text/javascript">
    $('.table-sort').dataTable({
        "aaSorting": [[ 1, "desc" ]],//默认第几个排序
        "bStateSave": true,//状态保存
        "aoColumnDefs": [
            //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
            {"orderable":false,"aTargets":[0,8]}// 制定列不参与排序
        ]
    });

    /*图片-添加*/
    function picture_add(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }

    function a(title,url){
        $.get(url,function(data){
            window.location.reload();
        });
    }
    function altpage(title,url){
        layer_show(title,url);
    }
    function confirm_action(message,url){
        layer.confirm(message,function(index){
            $.get(url,function (data) {
                window.location.reload();
            })
        });
    }


</script>