<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfCategory
 *
 * @property integer $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property boolean $display_order
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCategory whereDisplayOrder($value)
 */
class ConfCategory extends Model
{
    protected $table = 'conf_category';

    public $timestamps = true;

    protected $fillable = [
        'name'
    ];

    protected $guarded = [];

    /*获取分类名称*/
    static function getName($id){
        $category = self::whereId($id)->first();
        if (!$category){
            return false;
        }
        return $category->name;
    }
}