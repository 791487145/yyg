<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaAudit
 *
 * @property integer $id
 * @property integer $uid
 * @property string $active
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaAudit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaAudit extends Model
{
    protected $table = 'ta_audit';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'active',
        'content'
    ];

    protected $guarded = [];
}