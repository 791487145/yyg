<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsOptLog
 *
 * @property integer $id
 * @property boolean $type 1上架 -1删除 2下架  3添加库存
 * @property integer $uid 操作者id
 * @property integer $gid 商品id
 * @property string $content json
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereGid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsOptLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsOptLog extends Model
{
    protected $table = 'goods_opt_log';

    public $timestamps = true;

    const type_change_priceBuying = 31;
    const spec_change_before = 32;
    const spec_change_after = 33;
    protected $fillable = [
        'type',
        'uid',
        'gid',
        'content'
    ];

    protected $guarded = [];

        
}