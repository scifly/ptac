<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\{Corp, Group, Module, School, Tab};
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, Request};

/**
 * Class ExamIndexComposer
 * @package App\Http\ViewComposers
 */
class ModuleComposer {
    
    use ModelTrait;
    
    protected $module;
    
    /**
     * ModuleComposer constructor.
     * @param Module $module
     */
    function __construct(Module $module) {
        
        $this->module = $module;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $optionAll = [null => '全部'];
            switch (Auth::user()->role()) {
                case '运营':
                    $schools = $optionAll + School::whereEnabled(1)
                            ->pluck('name', 'id')->toArray();
                    break;
                case '企业':
                    $schools = $optionAll + School::where([
                            'enabled' => 1,
                            'corp_id' => (new Corp)->corpId()
                        ])->pluck('name', 'id')->toArray();
                    break;
                default:
                    break;
            }
            $tabs = $optionAll + Tab::where(['enabled' => 1, 'category' => 1])
                    ->pluck('comment', 'id')->toArray();
            $types = $optionAll + [0 => '基本', 1 => '增值'];
            $statuses = $optionAll + [0 => '未启用', 1 => '已启用'];
            $groups = $optionAll + [0 => '公用'] +
                Group::whereIn('name', ['监护人', '教职员工'])->pluck('name', 'id')->toArray();
            $data = [
                'titles' => [
                    '#', '名称',
                    isset($schools) ? [
                        'title' => '所属学校',
                        'html'  => $this->singleSelectList($schools, 'filter_school')
                    ] : '学校',
                    [
                        'title' => '控制器',
                        'html'  => $this->singleSelectList($tabs, 'filter_tab_id')
                    ],
                    'uri',
                    [
                        'title' => '所属角色',
                        'html'  => $this->singleSelectList($groups, 'filter_group_id')
                    ],
                    [
                        'title' => '类型',
                        'html'  => $this->singleSelectList($types, 'filter_isfree')
                    ],
                    [
                        'title' => '创建于',
                        'html'  => $this->inputDateTimeRange('创建于')
                    ],
                    [
                        'title' => '更新于',
                        'html'  => $this->inputDateTimeRange('更新于')
                    ],
                    '排序',
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->singleSelectList($statuses, 'filter_enabled')
                    ],
                ],
                'filter' => true
            ];
        } else {
            list($schools, $groups, $tabs, $media) = $this->module->compose();
            $data = [
                'schools' => $schools,
                'groups' => $groups,
                'tabs' => $tabs,
                'media' => $media
            ];
        }
    
        $view->with($data);
        
    }
    
}