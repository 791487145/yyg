@extends('layout_pop')
<style>
    .gift-text{width: 300px !important;}
    .gift-label{width: 105px;text-align: right;display: inline-block;}
    .btn-handle{margin:40px 150px !important; }
    .btn-handle .btn{width: 140px;text-align: center;margin-right: 50px;}
</style>
@section("content")
    <div class="pd-20">
        <form action="{{url('gift/add')}}" method="post" class="form form-horizontal" id="gift-add">
            <div class="row">
                <label class="gift-label">选择赠品商品：</label>
                <select name="gift_id" class="input-text gift-text gift">
                    @foreach($goods as $val)
                    <option value="{{$val->id}}">{{$val->title}}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <label class="gift-label">选择规格：</label>
                <select name="guide" class="input-text gift-text">
                    <option value="0">请选择规格</option>
                </select>
            </div>
            <div class="row">
                <label class="gift-label">库存：</label>
                200件
            </div>
            <div class="row">
                <label class="gift-label">供货价：</label>
                ￥20.00
            </div>

            <div class="row btn-handle">

                    <button type="submit" class="btn btn-success radius" ><i class="icon-ok"></i>确定</button>
                    <button type="button" class="btn btn-danger radius" onclick="layer_close();">取消</button>


            </div>
        </form>
    </div>
    <script>


    </script>
@endsection

@section("javascript")
    <script>
        $(function(){
            $("#gift-add").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'yes') {
                        layer.alert(data.msg,{icon:1,time:1000});
                        var content = '<div class="col-md-10 mt-10 gift-content"><div class="col-md-1">'+data.goods.id+'</div><div class="col-md-1">'+
                            '<img src="" alt="" width="80" height="80"/></div><div class="col-md-4">'+data.goods.title+'</div>'+
                            '<div class="col-md-2">'+data.goods.spec.name+'</div><div class="col-md-2">库存：200件 </div>' +
                            '<input type="hidden" name="gift[]" value="'+data.goods.id+'"/><div class="col-md-2 "><a class="c-blue gift-del">删除</a></div></div>';
                        parent.$('.gift-btn').before(content);
                        parent.$('.gift-id').remove();
                        parent.gift_remove();
                        layer_close();
                        //parent.location.replace(parent.location.href);
                    } else {
                        layer.alert(data.msg, {icon:2,time:5000});
                    }
                }
            });
            $('.gift').bind('change',function(){
                var url = '{{url('gift/guide')}}/'+$('.gift').val();
                var html = '';
                $.get(url,function(data){
                    $.each(data.guide,function(k,v){
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    })
                    $('[name=guide]').text('<option value="0">请选择规格</option>');
                    $('[name=guide]').append(html);
                })
            });

        });
    </script>
@endsection