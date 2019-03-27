<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\ActionType;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ActionComposer
 * @package App\Http\ViewComposers
 */
class ActionComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $optionAll = [null => '全部'];
            $data = [
                'titles' => [
                    '#', '名称', '方法', '路由', '控制器',
                    'View路径', 'js路径',
                    [
                        'title' => '请求类型',
                        'html' => $this->singleSelectList(
                            $optionAll + ActionType::pluck('name', 'id')->toArray(),
                            'filter_action_type'
                        )
                    ],
                    [
                        'title' => '功能类型',
                        'html' => $this->singleSelectList(
                            array_merge($optionAll, [0 => '后台', 1 => '前端', 2 => '其他']),
                            'filter_category'
                        )
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html' => $this->singleSelectList(
                            array_merge($optionAll, [0 => '已禁用', 1 => '已启用']),
                            'filter_enabled'
                        )
                    ]
                ],
                'filter' => true,
            ];
        } else {
            $actionTypeIds = explode(',', Action::find(Request::route('id'))->action_type_ids);
            $selectedActionTypes = ActionType::whereIn('id', $actionTypeIds)
                ->pluck('name', 'id')->toArray();
            $data = [
                'actionTypes'         => ActionType::pluck('name', 'id'),
                'tabs'                => Tab::pluck('name', 'id'),
                'selectedActionTypes' => $selectedActionTypes ?? null,
            ];
        }
        
        $view->with($data);
        
    }
    
}