<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfPavilionCity
 *
 * @property integer $id
 * @property integer $pavilion_id
 * @property integer $city_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionCity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionCity wherePavilionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionCity whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionCity whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfPavilionCity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConfPavilionCity extends Model
{
    protected $table = 'conf_pavilion_city';

    public $timestamps = true;

    protected $fillable = [
        'pavilion_id',
        'city_id'
    ];

    protected $guarded = [];

        
}