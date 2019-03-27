<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class GroupComposer
 * @package App\Http\ViewComposers
 */
class GroupComposer {
    
    use ModelTrait;
    
    protected $excludedActions = [
        '创建学校', '保存学校', '删除学校',
        '创建微网站', '保存微网站', '删除微网站',
    ];
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '名称', '所属学校', '所属企业', '备注', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $tabActions = [];
            $tabs = Tab::whereIn('group_id', [0, Group::whereName('学校')->first()->id])
                ->where('category', 0)->get();
            $schools = [];
            $menu = new Menu();
            $rootMenuId = $menu->rootId(true);
            switch (Menu::find($rootMenuId)->menuType->name) {
                case '根':
                    $schools = School::whereEnabled(1)
                        ->pluck('name', 'id')->toArray();
                    break;
                case '企业':
                    $corp = Corp::whereMenuId($rootMenuId)->first();
                    $schools = School::whereCorpId($corp->id)
                        ->where('enabled', 1)->pluck('name', 'id')->toArray();
                    break;
                case '学校':
                    $schools = School::whereMenuId($rootMenuId)
                        ->where('enabled', 1)->pluck('name', 'id')
                        ->toArray();
                    break;
                default:
                    break;
        
            }
            foreach ($tabs as $tab) {
                $actions = Action::whereTabId($tab->id)
                    ->get(['id', 'name', 'method']);
                $actionList = [];
                foreach ($actions as $action) {
                    if (!in_array(trim($action->name), $this->excludedActions)) {
                        $actionList[] = [
                            'id'     => $action->id,
                            'name'   => $action->name,
                            'method' => $action->method,
                        ];
                    }
                }
                $tabActions[] = [
                    'tab'     => ['id' => $tab->id, 'name' => $tab->comment],
                    'actions' => $actionList,
                ];
            }
            $data = [
                'tabActions' => $tabActions,
                'schools'    => $schools,
            ];
            if ($action == 'create') {
                $menu = new Menu();
                $currentMenuId = session('menuId');
                if ($this->schoolId()) {
                    $schools = School::find($this->schoolId())->pluck('name', 'id');
                } else if ($corpMenuId = $menu->menuId($currentMenuId, '企业')) {
                    $corpId = Corp::whereMenuId($corpMenuId)->first()->id;
                    $schools = School::whereCorpId($corpId)->where('enabled', 1)->pluck('name', 'id');
                } else {
                    $schools = School::whereEnabled(1)->pluck('name', 'id');
                }
                $data = array_merge($data, ['schools' => $schools]);
            } else {
                $group = Group::find(Request::route('id'));
                $data = array_merge($data, [
                    'selectedMenuIds'   => implode(',', $group->menus->pluck('id')->toArray()),
                    'selectedTabIds'    => $group->tabs->pluck('id')->toArray(),
                    'selectedActionIds' => $group->actions->pluck('id')->toArray(),
                ]);
            }
    
        }
        
        $view->with($data);
        
    }
    
}