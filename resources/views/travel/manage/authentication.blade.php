@extends('travel')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>个人设置</span><a href="login.html" class="headR">退出</a></h2>
            <form class="form">
                <div class="box">
                    <h4>实名认证</h4>
                    <table>
                        <tr>
                            <th>真实姓名：</th>
                            <td width="400"><input type="text"></td>
                            <th>身份证号码：</th>
                            <td width="400"><input type="text"><span class="pass"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>上传手持身份证：</th>
                            <td><div class="fileResult oneFileResult"><span><label class="fileUpload"><input type="file" accept="image/jpeg,image/png,image/gif"/></label></span></div><span class="pass"></td>
                        </tr>
                    </table>
                </div>
                <div class="footButton">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                    <input type="button" value="提交审核" onclick="popupConfirm('applySuccess')">
                </div>
            </form>
        </div>
    </div>
    </div>
    <div class="popupBg"></div>
    <div class="popupWrap confirmWrap applySuccess">
        <div class="title"><b>实名认证</b></div>
        <div>
            <p class="textC">恭喜你的实名认证成功提交~</p>
            <p>我们的工作人员会在1-2个工作日内尽快审核，请耐心等待......</p>
            <div class="buttonGroup">
                <input type="button" class="button close" value="确定">
            </div>
        </div>
    </div>
    @stop
