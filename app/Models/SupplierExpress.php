<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SupplierExpress
 *
 * @property integer $id
 * @property integer $supplier_id
 * @property string $title
 * @property string $total_amount
 * @property string $express_amount 盐
 * @property integer $state  0关闭，1开启
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierExpress whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierExpress whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierExpress whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierExpress whereTotalAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierExpress whereExpressAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierExpress whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierExpress whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierExpress whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SupplierExpress extends Model
{
    protected $table = 'supplier_express';

    const state_open = 1;
    const state_close = 0;

    public $timestamps = true;

    protected $fillable = [
        'supplier_id',
        'title',
        'total_amount',
        'express_amount',
        'state'
    ];

    protected $guarded = [];




}