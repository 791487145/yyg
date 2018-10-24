<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AppVersion
 *
 * @property integer $id
 * @property integer $uid 操作人id
 * @property string $name 名称ios/android
 * @property string $version 版本号
 * @property string $url 地址
 * @property string $content 更新说明
 * @property boolean $is_force 是否强制升级 1为强制更新
 * @property boolean $is_selected 0不是默认版本 1 是默认版本
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereIsForce($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereIsSelected($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AppVersion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AppVersion extends Model {

    const NAME_IOS = 'ios';
    const NAME_ANDROID = 'android';

    const IS_FORCE = 1;
    const NOT_FORCE = 0;

    public $table = 'app_version';

    const IS_SELECTED = 1;

    const NOT_SELECTED = 0;

    public $timestamps = true;

    protected $fillable = ['uid', 'name', 'version', 'url', 'content', 'is_force', 'is_selected'];

    protected $guarded = [];

    Static function getNameArr()
    {
        return array(
            self::NAME_IOS => 'ios',
            self::NAME_ANDROID => 'android'
        );
    }

    Static function getSelectedArr()
    {
        return array(
            self::IS_SELECTED => '是',
            self::NOT_SELECTED => '否',
        );
    }

    Static function getForceArr()
    {
        return array(
            self::IS_FORCE => '是',
            self::NOT_FORCE => '否',
        );
    }
}
