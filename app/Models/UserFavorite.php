<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserFavorite
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $goods_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $open_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserFavorite whereOpenId($value)
 */
class UserFavorite extends Model
{
    protected $table = 'user_favorite';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'goods_id'
    ];

    protected $guarded = [];

        
}