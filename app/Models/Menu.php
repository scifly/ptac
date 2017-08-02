<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Menu
 *
 * @property int $id
 * @property int|null $parent_id 父菜单ID
 * @property string $name 菜单名称
 * @property string|null $remark 菜单备注
 * @property int $school_id 所属学校ID
 * @property int|null $lft
 * @property int|null $rght
 * @property int|null $media_id 图片ID
 * @property int|null $action_id 对应的控制器action ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Menu whereActionId($value)
 * @method static Builder|Menu whereCreatedAt($value)
 * @method static Builder|Menu whereEnabled($value)
 * @method static Builder|Menu whereId($value)
 * @method static Builder|Menu whereLft($value)
 * @method static Builder|Menu whereMediaId($value)
 * @method static Builder|Menu whereName($value)
 * @method static Builder|Menu whereParentId($value)
 * @method static Builder|Menu whereRemark($value)
 * @method static Builder|Menu whereRght($value)
 * @method static Builder|Menu whereSchoolId($value)
 * @method static Builder|Menu whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Menu extends Model {
    
    protected $fillable = [
        'parent_id',
        'name',
        'remark',
        'school_id',
        'lft',
        'rght',
        'media_id',
        'action_id',
        'enabled'
    ];
    
    public function tabs() {
        
        return $this->belongsToMany('App\Models\Tab')
            ->withPivot('tab_order', 'enabled')
            ->withTimestamps();
        
    }
    
}
