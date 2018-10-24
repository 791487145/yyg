<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsImage
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsImage extends Model
{
    protected $table = 'goods_images';

    public $timestamps = true;

    protected $fillable = [
        'goods_id',
        'name'
    ];

    protected $guarded = [];

        
}