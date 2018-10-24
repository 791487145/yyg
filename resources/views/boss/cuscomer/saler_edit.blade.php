@extends('layout')
    <style>
        #all{
            margin-left: 5%;
            height: 60%;
            width: 90%;
            margin-top:30px;
        }
        .a{
            float: left;
            height: 78%;
            width: 48%;
        }

    </style>
@section("content")
    <div id="all">
        <div class="a" style="margin-right:4%;">
            <div>
                销售人姓名：{{$Users->name}}</br>
                电话：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$Users->email}}
            </div>
            <div style="margin-top:15px">
                <label>方法：</label>
                <p>1.输入销售人员姓名和手机号，点击生成独有的旅行社/导游的邀请码或链接。</p>
                <p> 2.截图给到销售人员保存至本地，或者收藏邀请链接</p>
                <p>3.由销售人员面对面给旅行社/导游扫码注册，注册成功后及为该销售人员的业绩。</p>
            </div>
            <div>
               {{-- <button onclick="a('http://{{env('TRAVEL_DOMAIN')}}/auth/register?code={{$Users->invite_code}}')" type="button" class="btn btn-success radius" id="user-save" name="route-save">
                    <i class="icon-ok"></i>
                    生成邀请旅行社链接
                </button>--}}
               {{-- <button onclick="a('http://'.env('TRAVEL_DOMAIN').'/auth/register?code={{$Users->invite_code}}')" type="button" class="btn btn-success radius" id="user-save" name="route-save" style="margin-bottom: 10px"><i class="icon-ok"></i> 生成邀请旅行社链接</button>--}}
                <button onclick="a('http://www.yyougo.com/app/sale-invitation-guide.php?sid={{$Users->id}}')" type="button" class="btn btn-success radius" id="user-save" name="route-save"><i class="icon-ok"></i> 生成邀请导游链接</button>
            </div>


        </div>
        <div class="a">
            <div id="aa">
                <div class="visible-print text-center">
                    {!! QrCode::size(200)->generate('http://'.env('TRAVEL_DOMAIN').'/auth/register?code='.$Users->invite_code); !!}
                </div>
            </div>
            <div id="ab">
                <div class="visible-print text-center">
                    {!! QrCode::size(200)->generate('http://www.yyougo.com/app/sale-invitation-guide.php?sid='.$Users->id); !!}
                </div>
            </div>
            <div>
                邀请链接：<br/>
                <input id="url" style="width:80%" type="text" class="btn btn-default radius"  name="url" value="http://{{env('TRAVEL_DOMAIN')}}/auth/register?code={{$Users->invite_code}}'" readonly >
                <button  onclick="copyUrl2()" type="button" class="btn btn-success radius" id="user-save" name="route-save"><i class="icon-ok"></i> 复制</button>
            </div>
        </div>
    </div>
@endsection

@section("javascript")
    <script>
        $("#aa").hide();
        $("#ab").show();
        function a(url){
            $("#aa").hide();
            $("#ab").show();
            $("#url").val(url);
        }
        a('http://www.yyougo.com/app/sale-invitation-guide.php?sid={{$Users->id}}');
     

        function copyUrl2()
        {
            var Url2=document.getElementById("url");
            Url2.select(); // 选择对象
            document.execCommand("Copy"); // 执行浏览器复制命令
            alert("已复制好，可贴粘。");
        }

    </script>
@endsection