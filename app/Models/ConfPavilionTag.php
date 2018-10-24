<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfPavilionTag
 *
 * @property integer $id
 * @property integer $pavilion_id
 * @property string $name
 * @property integer $goods_id
 * @property integer $display_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag wherePavilionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConfPavilionTag extends Model
{
    protected $table = 'conf_pavilion_tag';

    public $timestamps = true;

    protected $fillable = [
        'pavilion_id',
        'name',
        'goods_id',
        'display_order'
    ];

    protected $guarded = [];

        
}