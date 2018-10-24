<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsMaterialBase
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $supplier_id
 * @property string $content
 * @property integer $like_num
 * @property integer $forward_num
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereLikeNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereForwardNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialBase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsMaterialBase extends Model
{
    protected $table = 'goods_material_base';

    public $timestamps = true;

    protected $fillable = [
        'goods_id',
        'supplier_id',
        'content',
        'like_num',
        'forward_num'
    ];

    protected $guarded = [];

        
}