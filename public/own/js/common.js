//此函数为修改table中的排序
function display_order_update(obj, url, csrf) {
    var $this = $(obj).parent();
    var $val = "";
    var $html = $(obj).html();
    if($html != undefined){
        $val = $html;
    }
    $this.html('<input class="input-display-order" style="width:50px;" type="text" name="display_order" value="'+ $val +'"/>');
    $(".input-display-order").focus();
    $(".input-display-order").blur(function(){
        var display_order = $this.children(".input-display-order").val();
        // alert(display_order);
        if($val != display_order) {
            $.ajax({
                headers: { 'X-CSRF-TOKEN' : csrf },
                url: url,
                type:'POST',
                data: "display_order="+display_order,
                success:function(result){
                    if(result.ret == 'yes'){
                        layer.msg(result.msg,{icon:1,time:1000});
                        $this.html('<div ondblclick="display_order_update(this,\''+url+'\',\''+csrf+'\')"  style="width:100%;height:100%;" class="display-order">'+result.display_order+'</div>');
                    }else if(result.ret == 'no'){
                        layer.msg(result.msg,{icon:2,time:5000});
                    }else{
                        layer.msg('修改失败',{icon:2,time:5000});
                        location.replace(location.href);
                    }
                },
                error: function (XmlHttpRequest, textStatus, errorThrown) {
                    if(XmlHttpRequest.status == 404)
                    {
                        layer_show('页面不存在', '/error/404', "", "500");
                    } else {
                        layer_show('权限错误', '/error/403', '', '500');
                    }
                }
            });
        } else {
            $this.html('<div ondblclick="display_order_update(this,\''+url+'\',\''+csrf+'\')" style="width:100%;height:100%;" class="display-order">'+$val+'</div>');
        }
    });
}

/*table中的删除*/
function table_del(obj, action, url){
    layer.confirm(action+'删除后不可恢复，确认要删除吗？',function(index){
        $.ajax({
            url: url,
            type:'POST',
            success:function(result){
                if(result.ret == 'yes'){
                    $(obj).parents("tr").remove();
                    layer.msg('已删除!',{icon:1,time:1000});
                }else if(result.ret == 'no'){
                    layer.msg(result.msg,{icon:2,time:5000});
                }else{
                    layer.msg('删除失败',{icon:2,time:5000});
                    location.replace(location.href);
                }
            },
            error: function (XmlHttpRequest, textStatus, errorThrown) {
                if(XmlHttpRequest.status == 404)
                {
                    layer_show('页面不存在', '/error/404', "", "500");
                } else {
                    layer_show('权限错误', '/error/403', '', '500');
                }
            }
        });
    });
}

/*table中的修改*/
function table_edit(action, active, url){
    layer.confirm(action,function(index){
        $.ajax({
            url: url,
            type:'POST',
            success:function(result){
                if(result.ret == 'yes'){
                    layer.msg('已'+active+'!',{icon:1,time:1000});
                }else if(result.ret == 'no'){
                    layer.msg(result.msg,{icon:2,time:5000});
                }else{
                    layer.msg(active+'失败',{icon:2,time:5000});
                    location.replace(location.href);
                }
            },
            error: function (XmlHttpRequest, textStatus, errorThrown) {
                if(XmlHttpRequest.status == 404)
                {
                    layer_show('页面不存在', '/error/404', "", "500");
                } else {
                    layer_show('权限错误', '/error/403', '', '500');
                }
            }
        });
    });
}


