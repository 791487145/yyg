<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaBase
 *
 * @property integer $id
 * @property string $mobile
 * @property string $salt
 * @property string $password
 * @property string $ta_name
 * @property string $ta_logo
 * @property integer $ta_province_id
 * @property integer $ta_city_id
 * @property string $withdraw_name 提现姓名
 * @property string $withdraw_bank 提现银行
 * @property string $withdraw_sub_bank 提现分行
 * @property string $withdraw_card_number 提现银行卡
 * @property string $opt_name
 * @property string $opt_mobile
 * @property string $opt_id_card
 * @property string $opt_photo_1
 * @property string $opt_photo_2
 * @property integer $sale_id 销售id
 * @property string $invite_code 邀请码
 * @property string $self_invite_code 自己的邀请码
 * @property float $amount 金额
 * @property float $freeze_amount 冻结金额
 * @property boolean $state -1删除，0未审，1正常，4关停
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereSalt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereTaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereTaLogo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereTaProvinceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereTaCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptIdCard($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptPhoto1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereOptPhoto2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereSaleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereInviteCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereSelfInviteCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereFreezeAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaBase extends Model
{
    protected $table = 'ta_base';

    const SESSION_TA = 'travel';
    const STATE_VALID = 1;
    
    const  state_del = -1;
    const  state_no_check = 0;
    const  state_check = 1;
    const  state_close = 4;
    const state_withdraw_wait_audit = 11;


    public $timestamps = true;

    protected $fillable = [
        'mobile',
        'salt',
        'password',
        'ta_name',
        'ta_logo',
        'ta_province_id',
        'ta_city_id',
        'withdraw_name',
        'withdraw_bank',
        'withdraw_sub_bank',
        'withdraw_card_number',
        'opt_name',
        'opt_mobile',
        'opt_id_card',
        'opt_photo_1',
        'opt_photo_2',
        'sale_id',
        'invite_code',
        'self_invite_code',
        'amount',
        'freeze_amount',
        'state'
    ];

    protected $guarded = [];

        
}