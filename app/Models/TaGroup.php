<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaGroup
 *
 * @property integer $id
 * @property integer $ta_id
 * @property integer $guide_id 指派导游uid
 * @property string $title
 * @property string $num
 * @property string $is_sent_sms
 * @property string $is_sent_msg
 * @property \Carbon\Carbon $start_time
 * @property \Carbon\Carbon $end_time
 * @property boolean $state 0未开始，10已开始， 1已接团，2已结束
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereIsSentMsg($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereIsSentSms($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereEndTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereUmengResponse($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $umeng_response
 */
class TaGroup extends Model
{

    const is_sent_sms_yes = 1;
    const is_sent_sms_no = 0;

    const STATE_NO_START = 0;
    const STATE_YES_START = 10;
    const STATE_START = 1;
    const STATE_END = 2;


    protected $table = 'ta_group';

    public $timestamps = true;

    protected $fillable = [
        'ta_id',
        'guide_id',
        'title',
        'num',
        'is_sent_sms',
        'is_sent_msg',
        'state',
        'start_time',
        'end_time',
        'guide_name',
        'guide_mobile',
        'umeng_response',
    ];
    static public function getStateCN($state){
        $array = array(
            TaGroup::STATE_NO_START => '未开始',
            TaGroup::STATE_YES_START => '去接团',
            TaGroup::STATE_START => '正在接团',
            TaGroup::STATE_END => '已结束'
        );
        return isset($array[$state]) ? $array[$state] : '';
    }

    protected $guarded = [];

}