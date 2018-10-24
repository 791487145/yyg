<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Job
 *
 * @property integer $id
 * @property string $queue
 * @property string $payload
 * @property boolean $attempts
 * @property boolean $reserved
 * @property integer $reserved_at
 * @property integer $available_at
 * @property \Carbon\Carbon $created_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Job whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Job whereQueue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Job wherePayload($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Job whereAttempts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Job whereReserved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Job whereReservedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Job whereAvailableAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Job whereCreatedAt($value)
 * @mixin \Eloquent
 */
class Job extends Model
{
    protected $table = 'jobs';

    public $timestamps = true;

    protected $fillable = [
        'queue',
        'payload',
        'attempts',
        'reserved',
        'reserved_at',
        'available_at'
    ];

    protected $guarded = [];

        
}