<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\Group;
use App\Models\Icon;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class TabComposer
 * @package App\Http\ViewComposers
 */
class TabComposer {
    
    use ModelTrait;
    
    protected $icon, $action, $menu;
    
    /**
     * TabComposer constructor.
     * @param Icon $icon
     * @param Action $action
     * @param Menu $menu
     */
    function __construct(Icon $icon, Action $action, Menu $menu) {
        
        $this->icon = $icon;
        $this->action = $action;
        $this->menu = $menu;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $roles = Group::whereIn('name', ['运营', '企业', '学校'])->pluck('name', 'id')->toArray();
            $optionAll = [null => '全部'];
            $data = [
                'batch'  => true, # 需要批量操作
                'titles' => [
                    '#', '控制器', '名称',
                    [
                        'title' => '角色',
                        'html'  => $this->singleSelectList(
                            array_merge($optionAll, [0 => '所有'], $roles), 'filter_group'
                        ),
                    ],
                    '默认功能',
                    [
                        'title' => '创建于',
                        'html'  => $this->inputDateTimeRange('创建于'),
                    ],
                    [
                        'title' => '更新于',
                        'html'  => $this->inputDateTimeRange('更新于'),
                    ],
                    [
                        'title' => '类型',
                        'html'  => $this->singleSelectList(
                            array_merge($optionAll, [0 => '后台', 1 => '前端', 2 => '其他']), 'filter_category'
                        ),
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->singleSelectList(
                            array_merge($optionAll, [0 => '已禁用', 1 => '已启用']), 'filter_enabled'
                        ),
                    ],
                ],
                'filter' => true,
            ];
        } else {
            if (Request::route('id')) {
                $tab = Tab::find(Request::route('id'));
                $tabMenus = $tab->menus;
                $selectedMenus = [];
                foreach ($tabMenus as $menu) {
                    $selectedMenus[$menu->id] = $menu->name;
                }
            }
            $data = [
                'icons'         => $this->icon->icons(),
                'actions'       => $this->action->actions(),
                'groups'        => array_merge(
                    [0 => '所有'], Group::whereIn('name', ['运营', '企业', '学校'])->pluck('name', 'id')->toArray()
                ),
                'menus'         => $this->menu->leaves(1),
                'selectedMenus' => $selectedMenus ?? null,
            ];
        }
        
        $view->with($data);
        
    }
    
}