<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GuideBase
 *
 * @property integer $id
 * @property integer $uid user_base id
 * @property integer $ta_id 旅行社id
 * @property integer $sale_id 销售id
 * @property string $avatar 用户头像
 * @property string $store_cover 店铺封面
 * @property string $invite_code 邀请码
 * @property string $real_name
 * @property string $withdraw_name 提现姓名
 * @property string $withdraw_bank 提现银行
 * @property string $withdraw_sub_bank 提现分行
 * @property string $withdraw_card_number 提现银行卡
 * @property string $guide_no 导游证卡号
 * @property string $guide_photo_1
 * @property string $guide_photo_2
 * @property string $qrcode
 * @property string $wx_qrcode
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereSaleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereStoreCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereInviteCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereRealName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereGuideNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereGuidePhoto1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereGuidePhoto2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereQrcode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereWxQrcode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GuideBase extends Model
{
    protected $table = 'guide_base';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'ta_id',
        'sale_id',
        'avatar',
        'store_cover',
        'invite_code',
        'real_name',
        'withdraw_name',
        'withdraw_bank',
        'withdraw_sub_bank',
        'withdraw_card_number',
        'guide_no',
        'guide_photo_1',
        'guide_photo_2',
        'qrcode',
        'wx_qrcode'
    ];

    protected $guarded = [];

        
}