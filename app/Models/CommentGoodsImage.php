<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CommentGoodsImage
 *
 * @property integer $id
 * @property integer $comment_id 评论的id
 * @property string $image_name 用户评论商品时添加的图片
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGoodsImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGoodsImage whereCommentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGoodsImage whereImageName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGoodsImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CommentGoodsImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommentGoodsImage extends Model
{
    protected $table = 'comment_goods_image';

    public $timestamps = true;

    protected $fillable = [
        'comment_id',
        'image_name'
    ];

    protected $guarded = [];




}