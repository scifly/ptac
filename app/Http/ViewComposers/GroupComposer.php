<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\Tab;
use Illuminate\Contracts\View\View;

class GroupComposer {

    use ModelTrait;

    protected $excludedActions = [
        '创建学校', '保存学校', '删除学校',
        '创建微网站', '保存微网站', '删除微网站'
    ];

    public function compose(View $view) {

        $tabActions = [];
        $tabs = Tab::whereIn('group_id', [0, 3])->get();
        foreach ($tabs as $tab) {
            $actions = Action::whereController($tab->controller)
                ->get(['id', 'name', 'method']);
            $actionList = [];
            foreach ($actions as $action) {
                if (!in_array(trim($action->name), $this->excludedActions)) {
                    $actionList[] = [
                        'id' => $action->id,
                        'name' => $action->name,
                        'method' => $action->method
                    ];
                }
            }
            $tabActions[] = [
                'tab' => ['id' => $tab->id, 'name' => $tab->name],
                'actions' => $actionList,
            ];
        }
        
        $view->with([
            'tabActions' => $tabActions,
            'uris' => $this->uris()
        ]);
        
    }

}