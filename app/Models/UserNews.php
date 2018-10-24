<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserNews
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $news_id
 * @property integer $is_read 1已读，0未读
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereNewsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereIsRead($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserNews whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserNews extends Model
{
    const is_read_yes = 1;

    protected $table = 'user_news';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'news_id',
        'is_read'
    ];

    protected $guarded = [];

        
}