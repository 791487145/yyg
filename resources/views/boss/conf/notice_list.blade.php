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
    <div class="text-c" style="margin-top:10px;">
        <form method="post" id="form" action="/conf/confnotice">
            <div class="row cl mb-10">
                <label class="form-label col-xs-4 col-sm-2">标题：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" name="title" class="input-text" value="" placeholder="请输入标题">
                </div>
            </div>
            <div class="row cl mb-10">
                <label class="form-label col-xs-4 col-sm-2">内容：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <textarea name="content" cols="" id="textarea1" rows="" class="textarea" placeholder="请输入相关文字内容" ></textarea>
                </div>
            </div>
            <input type="button" onclick="document.getElementById('textarea1').value=''" value='清除文本' class="btn btn-success radius" id="" name=""/>
        <input type="submit" onclick="check()" class="btn btn-success radius" value="保存"  id="submit" name="submit"/>
        </form>
    </div>

    <div class="mt-20">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper no-footer"><div id="DataTables_Table_0_filter" class="dataTables_filter"></div><table class="table table-border table-bordered table-hover table-bg table-sort dataTable no-footer" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
                <thead>
                <tr class="text-c" role="row">
                    <th width="80px"  tabindex="0" aria-controls="DataTables_Table_0" colspan="2"  style="width: 80px;">消息内容</th>
                    <th width="100"  tabindex="0" aria-controls="DataTables_Table_0"  colspan="1"  style="width: 100px;">创建时间</th>
                    <th width="40"  tabindex="0" aria-controls="DataTables_Table_0"  colspan="1"  style="width: 40px;">发送时间</th>
                    <th width="90" tabindex="0" aria-controls="DataTables_Table_0"  colspan="1" style="width: 90px;">操作</th>
                </tr>
                </thead>
                <tbody>

                @foreach($platFormNotices as $platFormNotice)
                <tr class="text-c odd" role="row">
                    <td  colspan="2" width="200px" style="TABLE-LAYOUT:fixed;WORD-WRAP:break_word;text-align: left;"><h4>{{$platFormNotice->title}}</h4></p>{{$platFormNotice->content}}</td>
                    <td  colspan="1">{{$platFormNotice->created_at }}</td>
                    <td  colspan="1">{{ $platFormNotice->updated_at }}</td>
                    <td  colspan="1">
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
    @endsection
    @section("javascript")
        <script>
           function check(){
               $inputtext = $(".input-text").val();
               $textarea = $(".textarea").val();

               if($inputtext == ''){
                   alert('标题不能为空');
               }
               if($textarea == ''){
                   alert("内容不能为空");
               }

           }

            function a(title,url){
                $.get(url,function(data){
                    window.location.reload();
                });
            }

            function confirm_action(message,url){
                layer.confirm(message,function(index){
                    $.get(url,function (data) {
                        window.location.reload();
                    })
                });
            }
        </script>
@endsection