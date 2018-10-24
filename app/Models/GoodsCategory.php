<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsCategory
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $category_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsCategory extends Model
{
    protected $table = 'goods_category';

    public $timestamps = true;

    protected $fillable = [
        'goods_id',
        'category_id'
    ];

    protected $guarded = [];

        
}