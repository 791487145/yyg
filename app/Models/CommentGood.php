<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CommentGood
 *
 * @property integer $id
 * @property integer $uid 用户的id
 * @property integer $parent_id 追加评论时的commentid
 * @property integer $goods_id 商品的id
 * @property string $order_no 订单号
 * @property integer $spec_id 商品规格的id
 * @property string $comment 用户评论
 * @property string $reply_comment 商家回复用户评论
 * @property boolean $state 评论内容的状态 1:所有人都可见 , 2:仅自己可见
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereSpecId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereReplyComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGood whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommentGood extends Model
{
    protected $table = 'comment_goods';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'parent_id',
        'goods_id',
        'order_no',
        'spec_id',
        'comment',
        'reply_comment',
        'state'
    ];

    protected $guarded = [];

   


}