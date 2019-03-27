<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\PassageRule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class PassageRuleComposer
 * @package App\Http\ViewComposers
 */
class PassageRuleComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => [
                    '#', '名称', '规则id',
                    [
                        'title' => '起始日期',
                        'html'  => $this->inputDateTimeRange('起始日期', false),
                    ],
                    [
                        'title' => '结束日期',
                        'html'  => $this->inputDateTimeRange('结束日期', false),
                    ],
                    '适用范围', '时段1', '时段2', '时段3', '关联规则id',
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->singleSelectList(
                            [null => '全部'] + [0 => '禁用', 1 => '启用'], 'filter_enabled'
                        ),
                    ],
                ],
            ];
        } else {
            $data = array_combine(
                ['pr', 'weekdays', 'trs', 'doors', 'selectedDoors', 'ruleids'],
                (new PassageRule)->compose()
            );
        }
        
        $view->with($data);
        
    }
    
}