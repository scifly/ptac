<?php

namespace App\Models;

use App\Http\Requests\MenuRequest;
use App\Models\MenuTab as MenuTab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\Menu
 *
 * @property int $id
 * @property int|null $parent_id 父菜单ID
 * @property string $name 菜单名称
 * @property int|null $position 菜单所处位置
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
 * @method static Builder|Menu wherePosition($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tab[] $tabs
 * @property int|null $icon_id 图标ID
 * @property-read \Illuminate\Database\Eloquent\Collection|Menu[] $children
 * @property-read Menu|null $parent
 * @method static Builder|Menu whereIconId($value)
 * @property-read \App\Models\Icon|null $icon
 * @property-read \App\Models\Action|null $action
 * @property-read \App\Models\School $school
 */
class Menu extends Model {
    
    protected $fillable = [
        'parent_id',
        'name',
        'remark',
        'school_id',
        'position',
        'media_id',
        'action_id',
        'icon_id',
        'enabled'
    ];
    
    /**
     * 获取菜单所属学校
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() {
        
        return $this->belongsTo('App\Models\School');
        
    }
    
    /**
     * 获取菜单包含的卡片
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tabs() {
        
        return $this->belongsToMany(
            'App\Models\Tab',
            'menus_tabs',
            'menu_id',
            'tab_id'
        );
        
    }
    
    /**
     * 获取上级菜单
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {
        
        return $this->belongsTo(
            'App\Models\Menu',
            'parent_id'
        );
        
    }
    
    /**
     * 获取图标
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function icon() {
        
        return $this->belongsTo('App\Models\Icon');
        
    }
    
    /**
     * 获取所有叶节点菜单
     *
     * @param null $schoolId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function leaves($schoolId = NULL) {
        
        $leaves = [];
        $leafPath = [];
        $nodes = isset($schoolId) ? $this::whereSchoolId($schoolId)->get() : $this::all();
        foreach ($nodes as $node) {
            /** @noinspection PhpUndefinedMethodInspection */
            if (empty($node->children()->count())) {
                $path = $this->leafPath($node->id, $leafPath);
                $leaves[$node->id] = $path;
                $leafPath = [];
            }
        }
        return $leaves;
        
    }
    
    /**
     * 获取指定菜单的子菜单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() {
        
        return $this->hasMany(
            'App\Models\Menu',
            'parent_id',
            'id'
        );
        
    }
    
    /**
     * 获取指定菜单的完整路径
     *
     * @param $id
     * @param array $path
     * @return string
     */
    public function leafPath($id, array &$path) {
        
        $menu = $this->find($id);
        if (!isset($menu)) {
            return '';
        }
        $path[] = $menu->name;
        if (isset($menu->parent_id)) {
            $this->leafPath($menu->parent_id, $path);
        }
        krsort($path);
        return implode(' . ', $path);
        
    }
    
    /**
     * 创建Menu记录
     *
     * @param MenuRequest $request
     * @return bool|mixed
     */
    public function store(MenuRequest $request) {
        
        # 创建新的Menu记录及卡片绑定记录
        try {
            $exception = DB::transaction(function () use ($request) {
                $m = $this->create($request->all());
                $menuTab = new MenuTab();
                $tabIds = $request->input('tab_ids', []);
                $menuTab->storeByMenuId($m->id, $tabIds);
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 修改Menu记录
     *
     * @param MenuRequest $request
     * @param $menuId
     * @return bool|mixed
     */
    public function modify(MenuRequest $request, $menuId) {
        
        $menu = $this->find($menuId);
        if (!isset($menu)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($request, $menuId, $menu) {
                # 更新指定Menu记录
                $menu->update($request->all());
                # 更新与指定Menu记录绑定的卡片记录
                $tabIds = $request->input('tab_ids', []);
                $menuTab = new MenuTab();
                $menuTab::whereMenuId($menuId)->delete();
                $menuTab->storeByMenuId($menuId, $tabIds);
                /*foreach ($tabIds as $tabId) {
                    $menuTab->create([
                        'menu_id' => $menuId,
                        'tab_id' => $tabId,
                        'enabled' => 1
                    ]);
                }*/
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 删除Menu记录
     *
     * @param $menuId
     * @return bool|mixed
     */
    public function remove($menuId) {
        
        $menu = $this->find($menuId);
        if (!isset($menu)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($menuId, $menu) {
                # 删除指定的Menu记录
                $menu->delete();
                # 移除指定菜单与卡片的绑定记录
                MenuTab::whereMenuId($menuId)->delete();
                # 删除指定菜单的子所有菜单记录, 以及与卡片的绑定记录
                $subMenus = $this->where('parent_id', $menuId)->get();
                foreach ($subMenus as $subMenu) {
                    $this->remove($subMenu->id);
                }
                
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 更改菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @return bool
     */
    public function move($id, $parentId) {
        
        $menu = $this->find($id);
        if (!isset($menu)) {
            return false;
        }
        $menu->parent_id = $parentId === '#' ? NULL : intval($parentId);
        
        return $menu->save();
        
    }
    
    /**
     * 获取显示jstree的菜单数据
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree() {
    
        $defaultIconHtml = '<i class="fa fa-circle-o"></i>';
        $iconHtml = '<i class="%s"></i>';
        
        $menus = $this->get(['id', 'parent_id', 'name', 'position'])
            ->sortBy(['position'])->toArray();
        $data = [];
        foreach ($menus as $menu) {
            if (isset($menu['parent_id'])) {
                $m = $this->find($menu['id']);
                $icon = $m->icon;
                $menu['name'] =
                    ($icon ? sprintf($iconHtml, $icon->name) : $defaultIconHtml) .
                    '&nbsp;' . $menu['name'];
            }
            $parentId = isset($menu['parent_id']) ? $menu['parent_id'] : '#';
            $data[] = [
                'id' => $menu['id'],
                'parent' => $parentId,
                'text' => $menu['name']
            ];
        }
        return response()->json($data);
        
    }
    
    /**
     * 获取后台Menu的HTML字符串
     *
     * @param $activeMenuId
     * @return string
     */
    public function getMenuHtml($activeMenuId) {
    
        $parents = [$activeMenuId];
        $this->getParent($activeMenuId, $parents);
        # 不含子菜单的HTML模板
        $mSimple = '<li%s><a id="%s" href="%s"><i class="%s"></i> %s</a></li>';
        # 包含子菜单的HTML模板
        $mTree = <<<HTML
            <li class="treeview%s">
                <a href="#">
                    <i class="%s"></i> <span>%s</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
HTML;
        # ul列表尾部
        $mEnd = '</ul></li>';
        # 获取指定学校所有已启用的菜单对象
        $menus = $this->where('enabled', 1)
            ->where('id', '<>', 1)
            ->orderBy('position')->get();
        $menu = '';
        
        $level = 1;
        $parentId = 1;
        foreach ($menus as $m) {
            # 获取菜单ID
            $mId = $m->id;
            $isActive = in_array($mId, $parents);
            # 获取菜单名称
            $mName = $m->name;
            /** @noinspection PhpUndefinedMethodInspection */
            $hasChildren = $m->children->count();
            # 获取菜单对应的Icon
            $mIcon = $m->icon_id ? $m->icon->name : 'fa fa-circle-o';
            # 获取菜单对应的默认Route
            // $mUrl = $m->action_id ? '/urlshortener/public/' . $m->action->route : '#';
            // $mUrl = $mUrl ? $mUrl : '#';
            $mUrl = '/ptac/public/pages/' . $m->id;
            # 计算菜单所处的level
            $lvl = 0;
            $this->getMenuLevel($m, $lvl);
            $mLevel = $lvl;
        
            if ($m->parent_id == $parentId) {
                if ($hasChildren) {
                    $level += 1;
                    $menu .= sprintf(
                        $mTree,
                        $isActive ? ' active' : '',
                        $mIcon,
                        $mName
                    );
                    $parentId = $m->id;
                } else {
                    $menu .= sprintf(
                        $mSimple,
                        $isActive ? ' class="active"' : '',
                        $mId,
                        $mUrl,
                        $mIcon,
                        $mName
                    );
                }
            } else {
                $parentId = $m->parent_id;
                $level = $level - $mLevel;
                for ($i = 0; $i < $level; $i++) {
                    $menu .= $mEnd;
                }
                $level = $mLevel;
                if ($hasChildren) {
                    $level += 1;
                    $menu .= sprintf(
                        $mTree,
                        $isActive ? ' active' : '',
                        $mIcon,
                        $mName
                    );
                    $parentId = $m->id;
                } else {
                    $menu .= sprintf(
                        $mSimple,
                        $isActive ? ' class="active"' : '',
                        $mId,
                        $mUrl,
                        $mIcon,
                        $mName
                    );
                }
            }
        
        }
        if ($level > 1) {
            for ($i = 0; $i < $level - 1; $i++) {
                $menu .= $mEnd;
            }
        }
        
        return $menu;
    
    }
    
    /**
     * 获取后台菜单数组
     *
     * @param mixed $records 所有菜单对象
     * @param array $menus 菜单数组
     */
    public function getMenuArray($records, &$menus) {
        
        if (isset($records)) {
            $i = 0;
            foreach ($records as $r) {
                if (isset($r->action_id)) {
                    $action = Action::whereId($r->action_id)->get()[0];
                    $r->action = $action->toArray();
                }
                if (isset($r->icon_id)) {
                    $icon = Icon::whereId($r->icon_id)->get()[0];
                    $r->icon = $icon->toArray();
                }
                /** @noinspection PhpUndefinedMethodInspection */
                $menus[] = $r->toArray();
                $children = $r->children;
                /** @noinspection PhpUndefinedMethodInspection */
                $this->getMenus($children->sortBy('position'), $menus[$i]['children']);
                $i++;
            }
        }
        
    }
    
    
    /**
     * 计算指定菜单所处的level
     *
     * @param Menu $m
     * @param $lvl
     */
    private function getMenuLevel(Menu $m, &$lvl) {
        
        if ($m->parent) {
            $lvl += 1;
            $this->getMenuLevel($m->parent, $lvl);
        }
        
    }
    
    /**
     * 获取指定菜单所有上级菜单ID
     *
     * @param $menuId
     * @param array $parents
     */
    private function getParent($menuId, array &$parents) {
        
        $menu = $this->find($menuId);
        if ($menu->parent) {
            $id = $menu->parent->id;
            $parents[] = $id;
            $this->getParent($id, $parents);
        }
        
    }
    
}
