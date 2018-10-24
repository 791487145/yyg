<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserAddress
 *
 * @property integer $id
 * @property integer $uid
 * @property string $name
 * @property string $mobile
 * @property integer $province_id 省
 * @property integer $city_id 市
 * @property integer $district_id 区
 * @property string $address
 * @property boolean $is_default
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereProvinceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereDistrictId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereIsDefault($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAddress whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserAddress extends Model
{
    protected $table = 'user_address';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'name',
        'mobile',
        'province_id',
        'city_id',
        'district_id',
        'address',
        'is_default'
    ];

    protected $guarded = [];

        
}