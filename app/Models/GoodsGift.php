<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsGift
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $gift_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereGiftId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer $spec_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsGift whereSpecId($value)
 */
class GoodsGift extends Model
{
    protected $table = 'goods_gift';

    public $timestamps = true;

    protected $fillable = [
        'goods_id',
        'gift_id',
        'spec_id'
    ];

    protected $guarded = [];

        
}