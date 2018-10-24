@extends('travel')
@section('content')
    <style>
        .orderImportStep ul{background: url({{asset('/images/order_import_03.png')}}) no-repeat !important;}
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
                    <div class="upLoad">
                        <input type="button" class="green" value="下载游客信息录入模板">
                        <label class="file"><input type="file">导入Excel表格（已填写游客信息）</label>
                    </div>
                    <input class="submit" type="submit" value="确定并上传" onclick="popupConfirm('importSuccess')" style="display:none">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="popupBg"></div>
    <div class="popupWrap confirmWrap importSuccess">
        <div class="title"><b>指派导游</b></div>
        <div>
            <p class="conTit">恭喜，游客信息录入成功~</p>
            <p class="textC">指派导游为：李二狗</p>
            <p>为避免游客流失，请及时与李二狗联系，及时添加游客为下级成员！</p>
            <div class="buttonGroup">
                <input type="button" value="知道了">
            </div>
        </div>
    </div>
@stop