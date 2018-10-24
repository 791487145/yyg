<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfBank
 *
 * @property integer $id
 * @property integer $display_order
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBank whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBank whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBank whereName($value)
 * @mixin \Eloquent
 */
class ConfBank extends Model
{
    protected $table = 'conf_bank';

    public $timestamps = false;

    protected $fillable = [
        'display_order',
        'name'
    ];

    protected $guarded = [];

        
}