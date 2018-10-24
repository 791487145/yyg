<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WxReply
 *
 * @property integer $id
 * @property string $key_word
 * @property string $media_id
 * @property string $state 0:禁用；1：正常；-1：删除
 * @property string $response
 * @property string $type
 * @property string $remark
 * @property integer $parent_id
 * @property string $title
 * @property string $description
 * @property string $picurl
 * @property string $url
 * @property integer $category  1:图片；2：文字；6：图文;0默认自动回复
 * @property string $create_time
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereKeyWord($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereMediaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereResponse($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereCreateTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply wherePicUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxReply whereCategory($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WxReply extends Model
{


    protected $table = 'wx_reply';

    const Category_pic = 1;

    const StateNormal = 1;
    const StateStop = 0;

    public $timestamps = true;

    protected $fillable = [
        'key_word',
        'media_id',
        'state',
        'response',
        'type',
        'create_time',
        'remark',
        'parent_id',
        'title',
        'description',
        'picurl',
        'url',
        'category'
    ];
    protected $guarded = [];

    static public function getStateDescription($state){

        $stateArray = array(
            WxReply::StateNormal=> '正常',
            WxReply::StateStop=> '禁用',
        );
        return  isset($stateArray[$state]) ? $stateArray[$state] : '';
    }
    
}