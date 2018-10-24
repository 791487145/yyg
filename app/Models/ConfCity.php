<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfCity
 *
 * @property integer $id
 * @property string $name
 * @property string $zip_code
 * @property string $path
 * @property integer $parent_id
 * @property string $created
 * @property boolean $state
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereZipCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereCreated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfCity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConfCity extends Model
{
    const PROVINCE_PARENT_ID = 1;
    protected $table = 'conf_city';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'zip_code',
        'path',
        'parent_id',
        'created',
        'state'
    ];

    protected $guarded = [];
}