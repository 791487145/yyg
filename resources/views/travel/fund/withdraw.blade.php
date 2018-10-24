@extends('travel')
@section('content')

    <div class="rightCon">
        <div class="wrap">
            <h2><span>订单详情</span></h2>
            <form class="form orderDetail">
                <div class="box ">
                    <div class="orderStep">
                        <dl>
                            <dt>买家下单</dt>
                            <dd>2016-03-29   19:34:00</dd>
                        </dl>
                        <dl>
                            <dt>付款至香米家</dt>
                            <dd>2016-03-29   19:34:00</dd>
                        </dl>
                        <dl>
                            <dt>商家发货</dt>
                            <dd>2016-03-29   19:34:00</dd>
                        </dl>
                        <dl>
                            <dt>结算货款</dt>
                            <dd>2016-03-29   19:34:00</dd>
                        </dl>
                    </div>
                </div>
                <div class="box">
                    <h5>订单信息</h5>
                    <table>
                        <tr>
                            <td width="400">订单编号：12345678989</td>
                            <td width="400">数量：1</td>
                        </tr>
                        <tr>
                            <td>商品名称：焚枯食淡九</td>
                            <td>订单金额：20.00</td>
                        </tr>
                        <tr>
                            <td>零售价：20.00</td>
                            <td>付款时间：2016-03-29</td>
                        </tr>
                        <tr>
                            <td>规格：一盒装</td>
                            <td>付款方式：微信支持</td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <h5>赠品信息</h5>
                    <p>商品名称大家分开拉进来司法局你</p>
                    <p><span>零售价：20.00</span><span>规格：一盒装</span><span>数量：1</span></p>
                </div>
                <div class="box">
                    <h5>收货人信息</h5>
                    <p><span>永乐</span><span>13425698745</span><span>广东省深圳市南山区科技园</span></p>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>物流公司：</th>
                            <td width="280"><select><option>顺丰快递</option><option>优速快递</option><option>申通快递</option></select></td>
                            <th>物流单号：</th>
                            <td width="280"><input type="text"></td>
                            <td><a href="http://www.baidu.com/s?wd=EMS+9620047665365" target="_blank" class="button">手动查询物流信息</a></td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>买家留言：</th>
                        </tr>
                        <tr>
                            <td width="650">
                                <div><textarea></textarea></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="footButton">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                    <input type="submit" value="保存" class="button">
                </div>
            </form>
        </div>
    </div>
    </div>
    @stop
