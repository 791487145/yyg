<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Session
 *
 * @property integer $id
 * @property string $payload
 * @property integer $last_activity
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Session whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Session wherePayload($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Session whereLastActivity($value)
 * @mixin \Eloquent
 */
class Session extends Model
{
    protected $table = 'sessions';

    public $timestamps = false;

    protected $fillable = [
        'payload',
        'last_activity'
    ];

    protected $guarded = [];

        
}