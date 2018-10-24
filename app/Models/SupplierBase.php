<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SupplierBase
 *
 * @property integer $id
 * @property string $name
 * @property string $card_id
 * @property string $mobile
 * @property string $salt 盐
 * @property string $password
 * @property string $avatar
 * @property integer $province_id 省
 * @property integer $city_id 市
 * @property float $deposit 保证金
 * @property float $amount 金额
 * @property float $freeze_amount 冻结金额
 * @property string $store_name 供应商名称
 * @property string $store_logo 供应商LOGO
 * @property integer $store_province_id
 * @property integer $store_city_id
 * @property string $withdraw_name 提现姓名
 * @property string $withdraw_bank 提现银行
 * @property string $withdraw_sub_bank 提现分行
 * @property string $withdraw_card_number 提现银行卡
 * @property string $remark 备注
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereCardId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereSalt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereProvinceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereDeposit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereFreezeAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereStoreName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereStoreLogo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereStoreProvinceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereStoreCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereState($value)
 * @mixin \Eloquent
 * @property boolean $state 状态:1正常 -1禁用
 * @property boolean $is_pick_up 0.不支持， 1支持自提
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBase whereIsPickUp($value)
 **/


class SupplierBase extends Model
{
    const STATE_VALID = 1;
    const SESSION_SUPPLIER = 'supplier';

    protected $table = 'supplier_base';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'card_id',
        'mobile',
        'salt',
        'password',
        'avatar',
        'province_id',
        'city_id',
        'deposit',
        'amount',
        'freeze_amount',
        'store_name',
        'store_logo',
        'store_province_id',
        'store_city_id',
        'withdraw_name',
        'withdraw_bank',
        'withdraw_sub_bank',
        'withdraw_card_number',
        'remark',
        'state'
    ];

    protected $guarded = [];

}