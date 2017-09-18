<?php

namespace App\Models;

use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Models\MenuType 菜单类型
 *
 * @property int $id
 * @property string $name 菜单类型
 * @property string $remark 备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|MenuType whereCreatedAt($value)
 * @method static Builder|MenuType whereEnabled($value)
 * @method static Builder|MenuType whereId($value)
 * @method static Builder|MenuType whereName($value)
 * @method static Builder|MenuType whereRemark($value)
 * @method static Builder|MenuType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Collection|\App\Models\Menu[] $menus
 */
class MenuType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取指定菜单类型所包含的所有菜单对象
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus() { return $this->hasMany('App\Models\Menu'); }
    
    public function typeList($type) {
        
        $list = $this->pluck('name', 'id')->toArray();
        $types = collect($this->where('enabled', 1)->get(['name'])->toArray())
            ->flatten()->all();
        if (!in_array($type, $types)) { return false; }
        $allowedTypeList = [array_search('其他', $list) => '其他'];
        switch ($type) {
            case '根':
                $allowedTypeList[array_search('运营', $list)] = '运营';
                break;
            case '运营':
                $allowedTypeList[array_search('企业', $list)] = '企业';
                break;
            case '企业':
                $allowedTypeList[array_search('学校', $list)] = '学校';
                break;
            default:
                break;
                
        }
        return $allowedTypeList;
        
    }
    
    /**
     * 保存菜单类型
     *
     * @param array $data
     * @return bool
     */
    public function store(array $data) {
        
        $menuType = $this->create($data);
        return $menuType ? true : false;
        
    }
    
    /**
     * 更新菜单类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    public function modify(array $data, $id) {
        
        $menuType = $this->find($id);
        if (!$menuType) { return false; }
        return $menuType->update($data) ? true : false;
        
    }
    
    /**
     * 删除删除菜单类型
     *
     * @param $id
     * @return bool|null
     */
    public function remove($id) {
        
        $menuType = $this->find($id);
        if (!$menuType) { return false; }
        return $menuType->removable($this, $id)
            ? $menuType->delete() : false;
        
    }
    
}
