<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfHotWord
 *
 * @property integer $id
 * @property string $name
 * @property integer $url
 * @property integer $display_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfHotWord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConfHotWord extends Model
{
    protected $table = 'conf_hot_word';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'url',
        'display_order'
    ];

    protected $guarded = [];

        
}