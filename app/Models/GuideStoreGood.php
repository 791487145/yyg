<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GuideStoreGood
 *
 * @property integer $id
 * @property integer $guide_id
 * @property integer $goods_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideStoreGood whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GuideStoreGood extends Model
{
    protected $table = 'guide_store_goods';

    public $timestamps = true;

    protected $fillable = [
        'guide_id',
        'goods_id'
    ];

    protected $guarded = [];
}