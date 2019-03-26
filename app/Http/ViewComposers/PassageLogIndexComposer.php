<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Group;
use Illuminate\Contracts\View\View;

/**
 * Class PassageLogIndexComposer
 * @package App\Http\ViewComposers
 */
class PassageLogIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $roles = Group::whereIn('name', ['监护人', '学生'])->get()->merge(
            Group::where('school_id', $this->schoolId())->get()
        )->pluck('name', 'id')->toArray();
        $optionAll = [null => '全部'];
        $view->with([
            'buttons' => [
                'store'  => [
                    'id'    => 'store',
                    'label' => '采集数据',
                    'icon'  => 'fa fa-refresh',
                ],
                'export' => [
                    'id'    => 'batch-export',
                    'label' => '批量导出',
                    'icon'  => 'fa fa-download',
                ],
            ],
            'titles'  => [
                '#', '持卡人',
                [
                    'title' => '角色',
                    'html'  => $this->singleSelectList(
                        $optionAll + $roles, 'filter_role'
                    ),
                ],
                '规则',
                [
                    'title' => '方向',
                    'html'  => $this->singleSelectList(
                        $optionAll + [0 => '出', 1 => '进'], 'filter_direction'
                    ),
                ],
                '地点',
                [
                    'title' => '通行时间',
                    'html'  => $this->inputDateTimeRange('通行时间'),
                ],
                [
                    'title' => '状态',
                    'html'  => $this->singleSelectList(
                        $optionAll + [0 => '异常', 1 => '正常'], 'filter_status'
                    ),
                ],
            ],
        ]);
        
    }
    
}