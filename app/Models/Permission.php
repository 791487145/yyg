<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $parent_id 父级id
 * @property boolean $is_menu 为1时是菜单
 * @property string $icon 图标
 * @property string $name 唯一名称即：路由
 * @property string $display_name 显示名称
 * @property string $description 描述
 * @property integer $display_order 排序
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereIsMenu($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereIcon($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereUpdatedAt($value)
 */
class Permission extends Model
{
    const IS_MENU = 1;

    const PARENT_MENU = 0;

    protected $table = 'permissions';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'parent_id',
        'is_menu',
        'icon',
        'display_order',
    ];

    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
        
}