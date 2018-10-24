<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportTaSale
 */
class ReportTaSale extends Model
{
    protected $table = 'report_ta_sale';

    public $timestamps = true;

    protected $fillable = [
        'ta_id',
        'ta_name',
        'order_num',
        'total_sale'
    ];

    protected $guarded = [];

    {{getters}}

    {{setters}}


}