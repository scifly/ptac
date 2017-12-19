<?php

namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;

class GroupComposer {

    protected $tab, $action, $corp, $school;
//    protected $excludedTabs = [
//        '功能', '微信企业应用', '运营者', '企业', '图标', '图标类型',
//        '消息类型', '系统管理员', '学校类型', '卡片', '警告类型', '通信方式',
//        '部门类型'
//    ];
    protected $excludedActions = [
        '创建学校', '保存学校', '删除学校',
        '创建微网站', '保存微网站', '删除微网站'
    ];

    public function __construct(Tab $tab, Action $action, School $school) {

        $this->tab = $tab;
        $this->action = $action;
        $this->school = $school;

    }

    public function compose(View $view) {

        $tabActions = [];
        $tabs = $this->tab->whereIn('group_id', [0, 3])->get();
        foreach ($tabs as $tab) {
            $actions = $this->action->where('controller', $tab->controller)
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
        $view->with(['tabActions' => $tabActions]);
    }

}