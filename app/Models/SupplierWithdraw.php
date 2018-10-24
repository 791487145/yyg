<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SupplierWithdraw
 *
 * @property integer $id
 * @property integer $uid
 * @property string $withdraw_name
 * @property string $withdraw_bank
 * @property string $withdraw_sub_bank
 * @property string $withdraw_card_number
 * @property float $amount
 * @property float $balance
 * @property boolean $state 0未审核，1已审通过，2已打款，4已驳回
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereWithdrawName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereWithdrawBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereWithdrawSubBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereWithdrawCardNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierWithdraw whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SupplierWithdraw extends Model
{
    protected $table = 'supplier_withdraw';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'withdraw_name',
        'withdraw_bank',
        'withdraw_sub_bank',
        'withdraw_card_number',
        'amount',
        'balance',
        'state',
        'remark'
    ];

    protected $guarded = [];
    static function getStateCn($key){
        $sn = [0=>'未审核',1=>'已审通过',2=>'已打款',4=>'已驳回'];
        return $sn[$key];
    }
}