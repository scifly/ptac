<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Group;
use Illuminate\Contracts\View\View;

/**
 * Class PassageLogComposer
 * @package App\Http\ViewComposers
 */
class PassageLogComposer {
    
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
                    'html'  => $this->htmlSelect(
                        $optionAll + $roles, 'filter_role'
                    ),
                ],
                [
                    'title' => '记录类型',
                    'html' => $this->htmlSelect(
                        $optionAll + ['无记录', '刷卡记录', '门磁', '报警记录'],
                        'filter_category'
                    )
                ],
                [
                    'title' => '方向',
                    'html'  => $this->htmlSelect(
                        $optionAll + ['出', '进'], 'filter_direction'
                    ),
                ],
                '地点',
                [
                    'title' => '通行时间',
                    'html'  => $this->htmlDTRange('通行时间'),
                ],
                '故障原因',
                [
                    'title' => '状态',
                    'html'  => $this->htmlSelect(
                        $optionAll + ['异常', '正常'], 'filter_status'
                    ),
                ],
            ],
        ]);
        
    }
    
}