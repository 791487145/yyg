@extends('travel')
@section('content')
    <style>
        .popupWrap table {
            margin: 0px auto;
            width: 400px;
        }
        .popupWrap {
            width: 440px;
        }
        .search{backgound:url({{asset('images/search_w.png')}}) no-repeat right center #a8a7a7;cursor: pointer;    padding: 0 45px 0 0px !important;margin-right: 20px;}
        .search input{padding-left:15px;}
        .search-btn{display:block;width: 40px;height: 28px;position: absolute;right: 163px;top:6px;backgound:url({{asset('images/search_w.png')}}) no-repeat;z-index: 999;}
        .absolute{position: absolute;}
        .Validform_wrong{color: red; background: url(../images/error.png) no-repeat left center;padding-left: 20px;}
        .Validform_right{display: none;}
        .addguidebut{padding: 20px;}
        .addguidebut a{background:#e7641c;line-height: 30px;color: #fff;display: inline-block;width: 195px;text-align: center;}
/*         .addGuide{background: transparent url("../images/activation_box.png") no-repeat scroll 0% 0%;} */
        
        .statusTab{padding: 0 20px;margin: 0;height: auto;}
		.statusTab span{padding:5px 0;}
		.statusTab span a{color: #666;}
     </style>
    <div class="rightCon">
        <div class="wrap">
            <h2><span>导游管理</span></h2>
            <div class="addguidebut">
                <a href="javascript:void(0)" class="add" onclick="popupConfirm('addGuide')">+添加本旅行社导游</a>
                <a href="javascript:void(0)" class="add" onclick="popupConfirm('addGuideByHand')">+添加其他旅行社导游</a>
            </div>
            <div class="searchForm">
                <form>
                    <table>
                        <tr>
                            <th>手机号：</th>
                            <td>
                                <input type="text" name="mobile" value="{{isset($keywords['mobile'])?$keywords['mobile']:''}}">   
                            </td>
                            <th>导游姓名：</th>
                            <td>
                            	<input type="text" name="name" value="{{isset($keywords['name'])?$keywords['name']:''}}">
                            </td>
                            <th>按时间搜索：</th>
                            <td class="inputGroup">
                                <input type="text" id="timeStart" name="start_time" value="{{isset($keywords['start_time'])?$keywords['start_time']:''}}">
                                                                                                                至
                                <input type="text" id="timeEnd" name="end_time" value="{{isset($keywords['end_time'])?$keywords['end_time']:''}}">
                            </td>
                            <th>排序：</th>
                            <td>
                                <select name="guideorder" style="width: auto">
                                    <option value="0">全部</option>
                                    <option value="1" @if($keywords['guideorder'] == 1) selected="selected" @endif>按导游绑定游客数从高到低排序</option>
                                    <option value="2" @if($keywords['guideorder'] == 2) selected="selected" @endif>按导游绑定游客数从低到高排序</option>
                                    <option value="3" @if($keywords['guideorder'] == 3) selected="selected" @endif>按导游累计销售额从高到低排序</option>
                                    <option value="4" @if($keywords['guideorder'] == 4) selected="selected" @endif>按导游累计销售额从低到高排序</option>
                                </select>
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
                <a href="{{url('/manage/guides/1')}}"><span @if($flag == 1) class="active"  @endif>本旅行社导游列表</span></a>
                <a href="{{url('/manage/guides/0')}}"><span @if($flag == 0) class="active"  @endif>本社待激活及其他旅行社导游列表</span></a>
            </div>
            <table class="detailTable">
                <tr>
                    <th>编号</th>
                    <th>导游信息</th>
                    <th>直辖游客数</th>
                    <th>下级游客关注公众号数</th>
                    <th>累计销售额</th>
                    <th>添加时间</th>
                    <th>激活时间</th>
                    <th>操作</th>
                </tr>
                @forelse($guiders as $guider)
                    @if($flag == 1)
                        
                        <tr>
                            <td>{{$guider->guide_id}}</td>
                            <td id="{{$guider->mobile}}">{{$guider->name}} {{$guider->mobile}}</td>
                            <td>{{$guider->vistors_num}}</td>
                            <td>{{$guider->refNum}}</td>
                            <td>{{$guider->total_sales}}</td>
                            <td>{{$guider->created_at}}</td>
                            <td>{{isset($guider->time)?$guider->time:''}}</td>
                            <!--<td><a href="user-guide-detail.html">查看</a></td>-->
                            <td>
                                <a href="javascript:void(0)" onclick="setGroup({{$guider->guide_id}},'{{$guider->name}}',{{$guider->mobile}})">指派</a>
                            | 
                                <a href="javascript:void(0)" class="modify" onclick="editname('{{$guider->name}}',{{$guider->uid}},{{$guider->mobile}})">编辑</a>
                            </td>
                        </tr>
                        
                    @else
                        
                        <tr>
                            <td>{{$guider->guide_id}}</td>
                            <td id="{{$guider->mobile}}">{{$guider->name}} {{$guider->mobile}}</td>
                            <td>{{$guider->vistors_num}}</td>
                            <td>{{$guider->refNum}}</td>
                            <td>{{$guider->total_sales}}</td>
                            <td>{{$guider->created_at}}</td>
                            <td>{{isset($guider->time)?$guider->time:''}}</td>
                            <!--<td><a href="user-guide-detail.html">查看</a></td>-->
                            <td>
                                @if($guider->is_guide == 1)
                                    <a href="javascript:void(0)" style="display: line-block;width:100px;text-align:right;" onclick="setGroup({{$guider->guide_id}},'{{$guider->name}}',{{$guider->mobile}})">指派</a>
                                @else
                                    <a href="javascript:void(0)" style="display: line-block;width:100px;text-align:right;" onclick="popupConfirm('activationBox')">激活导游</a>
                                @endif
                                
                                | 
                                <a href="javascript:void(0)" class="modify" onclick="editname('{{$guider->name}}',{{$guider->uid}},{{$guider->mobile}})">编辑</a>
                            </td>
                        </tr>
                        
                    @endif
                @empty
                @endforelse
            </table>
            <div class="footPage">
                <p>共{{$guiders->lastPage()}}页,{{$guiders->total()}}条数据 ；每页显示{{$guiders->perPage()}}条数据</p>
                <div class="pageLink">
                    {!! $guiders->appends($keywords)->render() !!}
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>
<div class="popupBg"></div>

    <!-- 修改导游信息 -->
    <div class="popupWrap confirmWrap modifyGuide">
        <form action="{{url('manage/modifyAlias')}}" method="post" id="modifyGuide" style="position:relative">
        <div class="title"><b>编辑导游</b></div>
        <table>
            <tr>
                <th>修改姓名：</th>
                <td><input style="margin-bottom: 25px;" type="text" value="" name="modifynick_name" datatype="*2-16" maxlength="16" nullmsg="请输入导游名称" errormsg="请输入正确的姓名"> 可修改</td>
                <td class="absolute" style="top:110px;left:100px;"></td>
            </tr>
            
            <tr>
                <th>手机号码：</th>
                <td>
                    <input type="text" name='phonenum' value="" readonly="readonly" > 不可修改
                    <input type="hidden" name='uid' value="">
                </td>
                <td class="absolute"></td>
            </tr>

            <tr>
                <td colspan="2">
                    <p class="warning">注意：如果你添加的导游手机号为新用户，还需要激活该导游，指派任务才能成功指派至该导游。</p>
                    <p class="warning">如果你添加的导游手机号为已存在用户，无需激活，指派、任 务将直接下达至该导游账户中。</p>
                </td>
            </tr>
        </table>
        <div class="buttonGroup">
            <input type="button" class="cancel" id="cancel_button" value="取消">
            <input type="submit" class="submit" value="确定">
        </div>
        </form>
    </div>
    
    <!-- 修改导游信息 -->
    <div class="popupWrap activationBox">
        <div class="activationCon">
            <div class="codeImg">
                {!! QrCode::size(320)->generate($url['url']); !!}   
            </div>
            <p>截图保存至本地或复制如下链接，微信转发，扫码注册即可激活~</p>
            <dl>
                <dt>邀请链接：</dt>
                <dd><input type="text" value="{{$url['shotUrl']}}" id="copyTxt"><input type="button" value="复制" data-clipboard-action="copy" data-clipboard-target="#copyTxt" id="copyBtn"></dd>
            </dl>
            <div class="buttonGroup">
                <input type="button" class="close" value="返回">
            </div>
        </div>

    </div>
    <!-- 添加本旅行社导游信息 二维码的方式-->
    <div class="popupWrap addGuide">
        <div class="activationCon">
            <div class="codeImg" style="margin-top:10px;">
                {!! QrCode::size(320)->generate($url['url']); !!}
            </div>
            <p style="color:red">截图保存至本地或复制如下链接，微信转发，扫码注册即可激活~</p>
            <dl>
                <dt>邀请链接：</dt>
                <dd><input type="text" value="{{$url['shotUrl']}}" id="copyTxt"><input type="button" value="复制" data-clipboard-action="copy" data-clipboard-target="#copyTxt" id="copyBtn"></dd>
            </dl>
            <div class="buttonGroup">
                <input type="button" class="close" value="返回">
            </div>
        </div>
    </div>
    
    <!-- 添加其他旅行社导游 手动添加的方式-->
    <div class="popupWrap addGuideByHand">
        <form action="{{url('/manage/guideStore')}}" method="post" id="addGuideByHand" style="position:relative">
        <div class="title"><b>添加其他旅行社导游</b></div>
        <table>
            <tr>
                <th style="width:100px">添加导游：</th>
                <td><input style="margin-bottom: 25px;" type="text" value="" name="nick_name" datatype="*2-16" maxlength="16" nullmsg="请输入导游名称" errormsg="请输入正确的姓名"></td>
                <td class="absolute" style="top:110px;left:120px;"></td>
            </tr>
            
            <tr>
                <th>手机号码：</th>
                <td>
                    <input type="text" style="margin-bottom: 25px;" name='mobile' value="" datatype="*2-16" maxlength="11" nullmsg="请输入手机号码" errormsg="请输入正确手机号码">
                    <input type="hidden" name='uid' value="">
                </td>
                <td class="absolute" style="top:178px;left:120px;"></td>
            </tr>

            <tr>
                <td colspan="2">
                    <p class="warning">注意：如果你添加的导游手机号为新用户，还需要激活该导游，指派任务才能成功指派至该导游。</p>
                    <p class="warning">如果你添加的导游手机号为已存在用户，无需激活，指派、任 务将直接下达至该导游账户中。</p>
                </td>
            </tr>
        </table>
        <div class="buttonGroup">
            <input type="button" class="cancel" id="cancel_button" value="取消">
            <input type="submit" class="submit" value="确定">
        </div>
        </form>
    </div>
    <!-- 添加其他旅行社导游 手动添加的方式-->
    
    
    <!-- 指派信息开始 -->
    <div class="popupWrap  setGuideGroup" style="width:500px">
        <form action="{{url('manage/setGuiderGroup')}}" method="post" id="setGuideGroup" style="position:relative">
        <div class="title"><b>指派设置</b></div>
        <table style="width:500px">
            <tr>
                <th>旅行团名称：</th>
                <td>
                    <input style="margin-bottom: 25px;" type="text" value="" id="groupname" name="groupname" datatype="*1-16" maxlength="16" nullmsg="请输入旅行社名称" errormsg="请输入正确的旅行社名称格式"> 限制16个字符
                </td>
                <td class="absolute" style="top:110px;left:110px;"></td>
            </tr>
            
            <tr>
                <th>旅行团人数：</th>
                <td>
                    <input type="text" style="margin-bottom: 25px;" id="vistornum" name='vistornum' value="" datatype="*" nullmsg="请输入旅行团的人数" errormsg="请输入正确的人数格式"> 
                </td>
                <td class="absolute" style="top:175px;left:110px;"></td>
            </tr>
            <tr>
                <th>开始时间：</th>
                <td class="inputGroup">
                    <input type="text" name="starttime" style="margin-bottom: 25px;" id="time_start" value="" > 
                </td>
                <td class="absolute" style="top:240px;left:110px;"></td>
            </tr>
            <tr>
                <th>结束时间：</th>
                <td class="inputGroup">
                    <input type="text" name="endtime" style="margin-bottom: 25px;" id="time_end" value="" >
                    <input type="hidden" id="guideId" name="guide_id" value="">
                </td>
                <td class="absolute" style="top:315px;left:110px;"></td>
            </tr>
            <tr>
                <th>指派导游：</th>
                <td class="inputGroup">
                    <input type="text" style="margin-bottom: 25px;" id="guideName" name="guide_name" value="" readonly="readonly">
                </td>
                <td class="absolute" style="top:315px;left:110px;"></td>
            </tr>
            <tr>
                <th>电话号码：</th>
                <td class="inputGroup">
                    <input type="text" style="margin-bottom: 25px;" id="guideMobile" name="guide_mobile" value="" readonly="readonly">
                </td>
                <td class="absolute" ></td>
            </tr>

            
        </table>
        <div class="buttonGroup">
            <input type="button" class="cancel" id="cancel_button" value="取消">
            <input type="submit" class="submit" value="确定指派">
        </div>
        </form>
    </div>
    <!-- 指派信息结束 -->
    
    <script type="text/javascript" src="{{asset('lib/laydate/laydate.js')}}"></script>
    <script src="{{asset('lib/clipboard/clipboard.min.js')}}"></script>
    <script>

    //日期范围限制 导游搜索
    var start = {
        elem: '#timeStart',
        format: 'YYYY-MM-DD hh:ss:mm',
        min: '2000-01-01', //设定最小日期为当前日期
        max: '2099-06-16', //最大日期
        istime: true,
        istoday: false,
        choose: function(datas){
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    var end = {
        elem: '#timeEnd',
        format: 'YYYY-MM-DD hh:ss:mm',
        min: '2000-01-01',
        max: '2099-06-16',
        istime: true,
        istoday: false,
        choose: function(datas){
            start.max = datas; //结束日选好后，充值开始日的最大日期
        }
    };

    $('#cancel_button').click(function(){
    	  $('.Validform_checktip').html('').removeClass("Validform_wrong");
    })
    
    //指派旅行团的起止时间
    var start2 = {
            elem: '#time_start',
            format: 'YYYY-MM-DD hh:ss:mm',
            min: '2000-01-01', //设定最小日期为当前日期
            max: '2099-06-16', //最大日期
            istime: true,
            istoday: false,
            choose: function(datas){
            	end2.min = datas; //开始日选好后，重置结束日的最小日期
            	end2.start2 = datas //将结束日的初始值设定为开始日
            }
        };
        var end2 = {
            elem: '#time_end',
            format: 'YYYY-MM-DD hh:ss:mm',
            min: '2000-01-01',
            max: '2099-06-16',
            istime: true,
            istoday: false,
            choose: function(datas){
            	start2.max = datas; //结束日选好后，充值开始日的最大日期
            }
        };

    
    laydate(start);
    laydate(end);
    laydate(start2);
    laydate(end2);

    //先前的搜索导游的信息
    $('.export').click(function(){
        //var url = "{{url('/manage/export')}}";
        //url += "?<php echo $_SERVER['QUERY_STRING'];?>";
        //location.href = url;
        var length = $('.detailTable td').length;
        if(length > 0){
        	var url = "{{url('/manage/export')}}";
            location.href = url;
        }else{
        	layer.alert("当前没有数据")
        }

        
    }); 

       function editname(nickname,id,phone){
           //第一个是字符串
           $('[name=modifynick_name]').val(nickname);
           $('[name=phonenum]').val(phone);
           $('[name=uid]').val(id);
      	   popupConfirm('modifyGuide');
           
       }

       //指派团的信息
       function setGroup(guide_id,guide_name,guide_mobile){
   	       $("#guideId").val(guide_id);
   	       $("#guideName").val(guide_name);
   	       $("#guideMobile").val(guide_mobile);
      	   popupConfirm('setGuideGroup');
       }

       $('#cancel_button').click(function(){
    	  $('.Validform_checktip').html('').removeClass("Validform_wrong");
       })


    
        var clipboard = new Clipboard('#copyBtn');
        clipboard.on('success', function(e) {
            alert('复制成功');
        });
        clipboard.on('error', function(e) {
        });
        $(function () {
            $('.search-btn').click(function(){
                $('#search').submit();
            });
        });

        //进行修改导游名字的插件函数
        $("#modifyGuide").Validform({
            tiptype:2,
            ajaxPost:true,
            postonce:true,
            callback:function(data){
                if(data.ret == 'yes') {
                    //获取当前对象，并将内容进行更新
                    var phone = data.phone;
                    var name  = data.name;
                    var phoneid = '#'+phone;
                    $(phoneid).html(name+' '+phone);
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

        
        $("#addGuide").Validform({
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
        
        //进行指定派团
        var url = "{{url('manage/visitors')}}";
        $("#setGuideGroup").Validform({
            tiptype:2,
            ajaxPost:true,
            postonce:true,
            callback:function(data){
                if(data.ret == 'yes') {
                    var time_start = $('#time_start').val();
                    var time_end   = $('#time_end').val();
                    if(!time_start){
                        layer.alert('请输入开始时间');
                        return;
                    }
                    if(!time_end){
                        layer.alert('请输入结束时间');
                        return;
                    }
                    layer.open({
                        content: data.msg,
                        btn: ['确认'],
                        yes: function(index, layero) {
                                //确认后的跳转的地方
                                window.location.href = url;
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

               
        $("#addGuideByHand").Validform({
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
        
    </script>

    @stop
