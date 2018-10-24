@extends('travel')
@section('content')
    <style>
        .showSecondTable{background: url({{asset('/images/on_icon.png')}}) no-repeat right center;}
        .Validform_wrong{color: red; background: url(http://store.yyg.com/images/error.png) no-repeat left center;padding-left: 20px;}
        .Validform_right{display: none;}
        .addguidebut{padding: 20px;}
        .addguidebut a{background:#e7641c;line-height: 30px;color: #fff;display: inline-block;width: 98px;text-align: center;}
        .statusTab{padding: 0 20px;margin: 0;height: auto;}
		.statusTab span{width:80px;margin:0 10px;text-align: center;padding:5px 0;}
		.statusTab span a{color: #666;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <h2><span>建团管理</span>
                <div class="headRightButton">
                    
                </div>
            </h2>
            <!--<div class="addguidebut"><a href="javascript:void(0)" class="add" onclick="popupConfirm('addGroup')">+建团指派</a></div>-->
            <div class="searchForm">
                <form>
                    <table>
                        <tr>
                        	<th>手机号：</th>
                            <td>
                                <input type="text" name="guide_mobile" value="{{isset($keyword['guide_mobile'])?$keyword['guide_mobile']:''}}">  
                            </td>
                            <th>导游姓名：</th>
                            <td>
                                <input type="text" name="guide_name" value="{{isset($keyword['guide_name'])?$keyword['guide_name']:''}}">  
                            </td>
                            
                            <th>按时间搜索：</th>
                            <td class="inputGroup">
                                <input type="text" id="timeStart" name="start_time" value="{{isset($keyword['start_time']) ? ($keyword['start_time']) : ''}}">
                                                                                                   至
                                <input type="text" id="timeEnd" name="end_time" value="{{isset($keyword['end_time']) ? ($keyword['end_time']) : ''}}">
                            </td>
                        </tr>
                    </table>
					<div class="buttonGroup">
                        <input type="submit" value="搜索" class="gray">
                        <input type="button" value="导出" class="export">
                    </div>
                </form>
            </div>
        </div>
        <div class="wrap" style="margin-top: 15px;">
            <div class="statusTab">
                <a href="{{url('/manage/visitors/0')}}"><span @if($state == 0) class="active" @endif>未接团</span></a>
                <a href="{{url('/manage/visitors/1')}}"><span @if($state == 1) class="active" @endif>已接团</span></a>
                <a href="{{url('/manage/visitors/2')}}"><span @if($state == 2) class="active" @endif>已结束</span></a>
            </div>
            <table class="detailTable tourTable">
                <tr>
                    <th>序号</th>
                    <th>旅行团名称</th>
                    <th>导游姓名</th>
                    <th>手机号</th>
                    <th>该团绑定游客数（人）</th>
                    <th>该团销量（笔）</th>
                    <th>该团销售额（元）</th>
                    <th>操作</th>
                </tr>
                @forelse($groups as $group)
                <tr>
                    <td>{{$group->id}}</td>
                    <td>{{$group->title}}</td>
                    <td>{{$group->guide_name}}</td>
                    <td>{{$group->guide_mobile}}</td>
                    <td>{{$group->vistor_num}}</td>
                    <td>{{$group->groupSaleNum}}</td>
                    <td>{{$group->groupSalesAmount}}</td>
                    <td><a href="{{url('manage/visitors/groupOrdersDetail',$group->id)}}">查看</a></td>
                </tr>
                @empty
                    @endforelse

            </table>
            <div class="footPage">
                <p>共{{$groups->lastPage()}}页,{{$groups->total()}}条数据 ；每页显示{{$groups->perPage()}}条数据</p>
                <div class="pageLink">
                    {!! $groups->appends([
                        'guide'=>isset($data['guide'])?$data['guide']:0,
                        'start_time'=>isset($data['start_time'])?$data['start_time']:'',
                        'end_time'=>isset($data['end_time'])?$data['end_time']:'',
                    ])->render() !!}
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>
    <div class="popupBg"></div>
    <div class="popupWrap confirmWrap addGroup">
        <div class="title"><b>建团指派</b></div>
        <form method="post" action="{{url('manage/visitors/group')}}" id="group-add">
        <table>
            <tr>
                <th>旅行团名称：</th>
                <td><input type="text" maxlength="16" name="title" datatype="s1-16" maxlength="16" nullmsg="请输入旅行团名称" errormsg="请输入旅行团名称"></td>
                <td></td>
            </tr>
            <tr>
                <th>指派导游：</th>
                <td>
                    <select name="guide_id">
                        @forelse($guiders as $guider)
                            <option value="{{$guider->guide_id}}">{{$guider->name}} {{$guider->mobile}}</option>
                            @empty
                        @endforelse
                    </select>
                </td>
                <td></td>
            </tr>
            <tr>
                <th>旅行团人数：</th>
                <td><input type="text" name="num" datatype="n1-6" maxlength="6" nullmsg="请输入旅行团人数" errormsg="请输入旅行团人数"></td>
                <td></td>
            </tr>
            <tr>
                <th>开始时间：</th>
                <td><input type="text" id="popupTimeStart" name="start_time" ></td>
                <td></td>
            </tr>
            <tr>
                <th>结束时间：</th>
                <td><input type="text" id="popupTimeEnd" name="end_time" ></td>
                <td></td>
            </tr>
        </table>

        <div class="buttonGroup">
            <input type="button" class="cancel" value="取消">
            <input type="submit" class="submit" value="确定并指派">
        </div>
        </form>
    </div>
    <div class="popupWrap activationBox">
        <div class="activationCon">
            <div class="codeImg">
                {!! QrCode::size(320)->generate('http://www.yyougo.com/app/invitation-guide.php?taid='.$taId) !!}
            </div>
            <p>截图保存至本地或复制如下链接，微信转发，扫码注册即可激活~</p>
            <dl>
                <dt>邀请链接：</dt>
                <dd><input type="text" value="http://www.yyougo.com/app/invitation-guide.php?taid={{$taId}}" id="copyTxt"><input type="button" value="复制" data-clipboard-action="copy" data-clipboard-target="#copyTxt" id="copyBtn"></dd>
            </dl>
            <div class="buttonGroup">
                <input type="button" class="close" value="返回">
            </div>
        </div>

    </div>
        <script src="{{asset('/lib/laydate/laydate.js')}}"></script>
        <script type="text/javascript" src="{{asset('lib/clipboard/clipboard.min.js')}}"></script>
        <script>
            var clipboard = new Clipboard('#copyBtn');
            clipboard.on('success', function(e) {
                alert('复制成功');
            });
            clipboard.on('error', function(e) {
            });
            //日期范围限制
            var start = {
                elem: '#timeStart',
                format: 'YYYY-MM-DD hh:mm:ss',
                min: '2000-01-01 00:00:00', //设定最小日期为当前日期
                max: '2099-06-16 00:00:00', //最大日期
                istime: true,
                istoday: false,
                choose: function(datas){
                    end.min = datas; //开始日选好后，重置结束日的最小日期
                    end.start = datas //将结束日的初始值设定为开始日
                }
            };
            var end = {
                elem: '#timeEnd',
                format: 'YYYY-MM-DD hh:mm:ss',
                min: '2000-01-01 00:00:00',
                max: '2099-06-16 00:00:00',
                istime: true,
                istoday: false,
                choose: function(datas){
                    start.max = datas; //结束日选好后，充值开始日的最大日期
                }
            };
            laydate(start);
            laydate(end);

            //日期范围限制
            var popupTimeStart = {
                elem: '#popupTimeStart',
                format: 'YYYY-MM-DD hh:mm:ss',
                min: '2000-01-01 00:00:00', //设定最小日期为当前日期
                max: '2099-06-16 00:00:00', //最大日期
                istime: true,
                istoday: false,
                choose: function(datas){
                    end.min = datas; //开始日选好后，重置结束日的最小日期
                    end.start = datas //将结束日的初始值设定为开始日
                }
            };
            var popupTimeEnd = {
                elem: '#popupTimeEnd',
                format: 'YYYY-MM-DD hh:mm:ss',
                min: '2000-01-01 00:00:00',
                max: '2099-06-16 00:00:00',
                istime: true,
                istoday: false,
                choose: function(datas){
                    start.max = datas; //结束日选好后，充值开始日的最大日期
                }
            };
            laydate(popupTimeStart);
            laydate(popupTimeEnd);

            $("#group-add").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'yes') {
                        layer.open({
                            content: data.msg,
                            btn: ['确认'],
                            yes: function(index, layero) {
                                window.location.reload();
                            },
                            cancel: function() {
                                window.location.reload()
                            }
                        });
                    } else {
                        layer.alert(data.msg, {icon:2,time:5000});
                    }
                }
            });

            $('.export').click(function(){
                //var url = "{{url('/manage/export')}}";
                //url += "?<php echo $_SERVER['QUERY_STRING'];?>";
                //location.href = url;

                var length = $('.detailTable td').length;
                if(length > 0){
                	var url = "{{url('/manage/exportGroup')}}";
                    location.href = url;
                }else{
                	layer.alert("当前没有数据")
                }

                
            });

            
        </script>
@stop
