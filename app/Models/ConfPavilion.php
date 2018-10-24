<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfPavilion
 *
 * @property integer $id
 * @property string $name
 * @property string $cover
 * @property string $new_cover
 * @property string $background
 * @property integer $display_order
 * @property integer $state
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon  $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereNewCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereBackground($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilion whereDescription($value)
 */
class ConfPavilion extends Model
{
    protected $table = 'conf_pavilion';

    public $timestamps = true;

    const state_online = 1;
    const state_del = -1;

    protected $fillable = [
        'name',
        'cover',
        'new_cover',
        'background',
        'display_order',
        'state',
        'description',

    ];

    protected $guarded = [];

    /*获取分馆名称*/
    static function getName($id){
        $pavilion = self::whereId($id)->first();
        return $pavilion->name;
    }
        
}