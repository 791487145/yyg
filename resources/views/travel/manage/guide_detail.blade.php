@extends('travel')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>查看详情</span>
                <div class="headRightButton">
                    <label class="search"><input type="text" placeholder="请输入关键字搜索"></label>
                </div>
            </h2>
            <div class="box guideDetail">
                <dl>
                    <dt>导游姓名：</dt>
                    <dd>邱太辉</dd>
                </dl>
                <dl>
                    <dt>直辖游客数：</dt>
                    <dd>21</dd>
                </dl>
                <dl>
                    <dt>目前排名：</dt>
                    <dd>第一名</dd>
                </dl>
                <dl>
                    <dt>注册时间：</dt>
                    <dd>2016-03-05   15:05</dd>
                </dl>
                <dl>
                    <dt>手机号码：</dt>
                    <dd>15803621564</dd>
                </dl>
                <dl>
                    <dt>累计销售额：</dt>
                    <dd>2136.00</dd>
                </dl>
            </div>
            <h6>直辖游客列表</h6>
            <table class="detailTable">
                <tr>
                    <th>序号</th>
                    <th>游客姓名</th>
                    <th>注册时间</th>
                    <th>今日成交笔数</th>
                    <th>今日成交额</th>
                    <th>累计成交额</th>
                </tr>
                <tr>
                    <td>2</td>
                    <td>李二蛋</td>
                    <td>2016-08-06<br>17:00:00</td>
                    <td>256</td>
                    <td>256.00</td>
                    <td>256.00</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>杨洋</td>
                    <td>2016-08-06<br>17:00:00</td>
                    <td>15</td>
                    <td>1500.00</td>
                    <td>1500.00</td>
                </tr>
            </table>
            <div class="footButton">
                <input type="button" value="返回" onclick="javascript:history.back(-1);">
            </div>
            <div class="footPage">
                <p>共7页,139条数据 ；每页显示20条数据</p>
                <div class="pageLink">
                    <ul>
                        <li><a href="">上一页</a></li>
                        <li><a href="">1</a></li>
                        <li><a href="">2</a></li>
                        <li>...</li>
                        <li><a href="">6</a></li>
                        <li><a href="">7</a></li>
                        <li><a href="">下一页</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
    @stop
