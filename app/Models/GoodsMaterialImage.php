<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsMaterialImage
 *
 * @property integer $id
 * @property integer $material_id
 * @property string $image_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereMaterialId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereImageName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsMaterialImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsMaterialImage extends Model
{
    protected $table = 'goods_material_image';

    public $timestamps = true;

    protected $fillable = [
        'material_id',
        'image_name'
    ];

    protected $guarded = [];

        
}