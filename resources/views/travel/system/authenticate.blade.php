@extends('travel')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>个人设置</span><a href="{{url('auth/logout')}}" class="headR">退出</a></h2>

                <div class="box">
                    <h4>实名认证</h4>
                    <table>
                        <tr>
                            <th>真实姓名：</th>
                            <td width="400">{{isset($user->opt_name)?$user->opt_name:''}}</td>
                            <th>身份证号码：</th>
                            <td width="400">{{isset($user->opt_id_card)?$user->opt_id_card:''}}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>正面手持身份证：</th>
                            <td>
                                <div class="fileResult oneFileResult">
                                    <span class="opt-photo-1"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{isset($user->opt_photo_1)?$user->opt_photo_1:''}}" width="150" height="150"></span>
                                </div>
                                <span class="pass"></span>
                            </td>
                        </tr>
                        <tr>
                            <th>反面手持身份证：</th>
                            <td>
                                <div class="fileResult oneFileResult">
                                    <span class="opt-photo-2"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{isset($user->opt_photo_2)?$user->opt_photo_2:''}}" width="150" height="150"></span>
                                </div>
                                <span class="pass"></span>
                            </td>
                        </tr>
                        <tr>
                            <th>审核状态：</th>
                            <td>
                                @if($user->state == 1)
                                    <span class="status">已认证</span>
                                @elseif($user->state == 2)
                                    <span class="status">正在审核中</span>
                                @elseif($user->state == 3)
                                    <span class="status">审核不通过，重新提交实名认证</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
        </div>
    </div>
    </div>
@stop
