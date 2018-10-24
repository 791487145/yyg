<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PermissionRole
 *
 * @mixin \Eloquent
 * @property integer $permission_id
 * @property integer $role_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PermissionRole wherePermissionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PermissionRole whereRoleId($value)
 */
class PermissionRole extends Model
{
    protected $table = 'permission_role';

    public $timestamps = false;

    protected $fillable = [
        'permission_id',
        'role_id'
    ];

    protected $guarded = [];

        
}