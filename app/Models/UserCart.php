<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserCart
 *
 * @property integer $id
 * @property string $uid
 * @property string $open_id
 * @property integer supplier_id
 * @property integer $goods_id
 * @property integer $spec_id
 * @property integer $num
 * @property integer $ta_id
 * @property integer $guide_id
 * @property integer $is_selected
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereSpecId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereIsSelected($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserCart whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserCart extends Model
{
    protected $table = 'user_cart';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'open_id',
        'supplier_id',
        'goods_id',
        'spec_id',
        'num',
        'ta_id',
        'guide_id',
        'is_selected'
    ];

    protected $guarded = [];

        
}