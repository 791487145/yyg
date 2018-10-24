@extends('supplier')
@section('content')
    <style>
        .swfupload{left:0;}
        .uploadify-queue {
            display: none;
        }
    </style>
    <div class="rightCon">
        <div class="wrap">
            <h2><span>批量导入订单发货</span></h2>
            <div class="orderImport">
                <div class="orderImportStep">
                    <ul>
                        <li class="do">导出批量发货订单并填写物流信息</li>
                        <li>导入Excel表格（已填写物流信息）</li>
                        <li>上传并批量发货</li>
                    </ul>
                </div>

                <div class="orderImportButton">
                    <label class="file">
                        <input type="file" id="uploadify">
                    </label>
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                </div>
            </div>
        </div>

        <div class="popupBg"></div>
        <div class="popupWrap confirmWrap importSuccess">
            <div class="title"><b>批量发货</b></div>
            <div>
                <h2>发货提示</h2>
                <p class="textC">恭喜，此次批量发货全部成功~</p>
                <div class="buttonGroup">
                    <input type="button" value="知道了" class="import-success">
                </div>
            </div>
        </div>
        <div class="popupWrap confirmWrap importFail">
            <div class="title"><b>批量发货</b></div>
            <div>
                <h2 class="red">失败记录</h2>
                <div class="failBox">
                    <p>批量发货未成功记录，请仔细检查物流公司和物流单号是否有错，重新上传发货~</p>
                    <div class="scroll">
                        1.订单编号xxxxxxx,商品名称商品米那个人看见，发货不成功<br>
                        2.订单编号xxxxxxx,商品名称商品米那个人看见，发货不成功<br>
                        3.订单编号xxxxxxx,商品名称商品米那个人看见，发货不成功<br>
                    </div>
                </div>
                <div class="buttonGroup">
                    <input type="button" value="知道了" class="cancel">
                </div>
            </div>
        </div>


    </div>
    </div>
    <script src="{{ asset('lib/uploadify/jquery.uploadify.min.js') }}" type="text/javascript"></script>

    <script>
        //批量导入
        $("#test").change(function () {
            var filename = getFileName($(this).val());
            function getFileName(o) {
                var pos = o.lastIndexOf("\\");
                return o.substring(pos + 1);
            }

        });

        <?php $timestamp = time();?>
        $(function () {
            $('#uploadify').uploadify({
                'formData': {
                    'timestamp': '<?php echo $timestamp;?>',
                    'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'swf': '{{ asset('lib/uploadify/uploadify.swf') }}',
                'uploader': '{{ url('/order/import') }}',
                'buttonText': '导入Excel表格（已填写物流信息）',
                'fileTypeDesc': 'filetypedesc',
                'fileTypeExts': '*.xls; *.xlsx; *.csv',
                'height': 36,
                'width': 316,
                'onUploadSuccess': function (file, data, response) {
                    data = jQuery.parseJSON(data);
                    if(data.ret == 'yes') {
                        layer.confirm('确认上传文件并批量发货吗？',function(o){
                            $.post('/order/importexcel/?filename='+data.filename,function(data){
                                if(data.ret == "yes"){
                                    popupConfirm('importSuccess');
                                    $(".layui-layer-close1").click();
                                }else{
                                    $(".layui-layer-close1").click();
                                    var fail = '';
                                    $.each(data.msg,function(k,v){
                                        fail += v+'<br/>';
                                    });
                                    $('.importFail .scroll').html(fail);
                                    popupConfirm('importFail');
                                }
                            })
                        });
                    }
                }
            });
            $(".popupWrap .buttonGroup .cancel").click(function(){
                $(".popupBg,.popupWrap").hide();
            });
            $(".popupWrap .buttonGroup .import-success").click(function(){
                window.location.href = '{{url('order/deliverys')}}';
            });
        })
    </script>
    @stop
