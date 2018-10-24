@extends('wx.layout')
@section('title')
    选择地址
@endsection
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">选择地址</div>
</div>
    <div class="content addressList">
    @if($UserAddresses == '')

    @endif
    @if($UserAddresses != '')
        @foreach($UserAddresses as $k=>$UserAddres)
        <div id="{{$UserAddres->id}}" class="box lineB mb-6">
            <a href="javascript:void(0)" onclick="check({{$UserAddres->id}})">
                <div class="info">
                    <div class="base">
                    <dl class="name">
                        <dt>姓名：</dt>
                        <dd>{{$UserAddres->name}}</dd>
                    </dl>
                    <dl class="tel">
                        <dt>联系方式：</dt>
                        <dd>{{$UserAddres->mobile}}</dd>
                    </dl>
                </div>
                <div class="address">
                    <dl>
                        <dt>地址：</dt>
                        <dd>{{$UserAddres->province}}{{$UserAddres->city}}{{$UserAddres->district}}{{$UserAddres->address}}</dd>
                    </dl>
                </div>
            </div>
            </a>
            <div class="bottom lineT">
                <input onclick="check({{$UserAddres->id}})" type="radio" class="is_default" name="is_default" value="1" id="radio{{$k+1}}" {{($UserAddres->is_default == 1) ? 'checked' : ''}}><label for="radio{{$k+1}}">设为默认收货地址</label>
                <a href="javascript:void(0)" onclick="del({{$UserAddres->id}})" class="delete lineL">删除</a>
                <a href="/address/{{$UserAddres->id}}" class="edit">编辑</a>
            </div>
        </div>
        @endforeach
    @endif
    </div>
    <div class="footButton">
    <input type="button" class="button" value="新增地址" onclick="location.href='/address'">
</div>

@endsection
@section('javascript')
    <script>
        function del(id){
            $.get('/addresses/del',{id:id},function(msg){
                var is_default = $('input[name="is_default"]:checked').val();
                if(is_default==undefined){
                    $(".addressList .is_default").eq(0).click();
                }
            })
        }

       function  check(id){
           var is_default = $('input[name="is_default"]:checked').val();
           $.get('/addresses/check',{id:id,is_default:is_default},function(msg){
               if(msg.ret == 'two'){
                   location.href='/order/cart/0';
               }

           })
       }

    </script>
@endsection
