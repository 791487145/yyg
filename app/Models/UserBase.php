<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserBase
 *
 * @property integer $id
 * @property string $nick_name
 * @property string $mobile
 * @property string $password
 * @property boolean $is_guide
 * @property float $amount
 * @property float $freeze_amount
 * @property boolean $state 0未审，1正常，2审核未通过， 4关停 ,   11待审核
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereNickName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereSalt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereIsGuide($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereFreezeAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $salt
 * @property string $token token
 * @property boolean $sex 1.男 2.女
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereSex($value)
 * @property string $lng
 * @property string $lat
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereLng($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBase whereLat($value)
 */
class UserBase extends Model
{
    protected $table = 'user_base';

    public $timestamps = true;

    const is_guide_yes = 1;
    const is_guide_no = 0;

    const state_checking = 0;
    const state_upload_2cert = 11;
    const state_check = 1;
    const state_close = 4;
    const state_no_check = 2;
    const state_zp = 5; //指派


    protected $fillable = [
        'nick_name',
        'mobile',
        'password',
        'salt',
        'is_guide',
        'amount',
        'freeze_amount',
        'state'
    ];

    protected $guarded = [];


    static function getStateCnByState($state){
        $stateArray = array(
            '0'=>'待上传身份认证',
            '11'=>'待审核',
            '1'=>'正常',
            '2'=>'审核未通过',
            '4'=>'关停');
        return isset($stateArray[$state]) ? $stateArray[$state] : '';
    }
}