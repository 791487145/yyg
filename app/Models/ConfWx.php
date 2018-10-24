<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfWx
 *
 * @property integer $id
 * @property string $accessToken
 * @property string $jsApiTicket
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfWx whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfWx whereAccessToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfWx wherejsApiTicket($value)
 * @mixin \Eloquent
 * @property string $access_token
 * @property string $jsapiticket
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfWx whereJsapiticket($value)
 */
class ConfWx extends Model
{
    protected $table = 'conf_wx';

    public $timestamps = false;

    protected $fillable = [
        'access_token',
        'jsapiticket'
    ];

    protected $guarded = [];

        
}