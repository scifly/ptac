<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;

/**
 * Class GroupComposer
 * @package App\Http\ViewComposers
 */
class GroupComposer {
    
    protected $excludedActions = [
        '创建学校', '保存学校', '删除学校',
        '创建微网站', '保存微网站', '删除微网站',
    ];
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $tabActions = [];
        $tabs = Tab::whereIn('group_id', [0, Group::whereName('学校')->first()->id])->get();
        $schools = [];
        $menu = new Menu();
        $rootMenuId = $menu->rootMenuId(true);
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
            $actions = Action::whereController($tab->controller)
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
                'tab'     => ['id' => $tab->id, 'name' => $tab->name],
                'actions' => $actionList,
            ];
        }
        $view->with([
            'tabActions' => $tabActions,
            'schools'    => $schools,
        ]);
        
    }
    
}