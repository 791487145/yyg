<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GuideAudit
 *
 * @property integer $id
 * @property integer $uid
 * @property string $active
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideAudit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GuideAudit extends Model
{
    protected $table = 'guide_audit';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'active',
        'content'
    ];

    protected $guarded = [];

}