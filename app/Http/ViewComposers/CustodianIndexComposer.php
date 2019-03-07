<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

/**
 * Class CustodianIndexComposer
 * @package App\Http\ViewComposers
 */
class CustodianIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        reset($grades);
        $classes = Squad::whereGradeId(key($grades))
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        $view->with([
            'buttons'        => [
                'issue' => [
                    'id' => 'issue',
                    'label' => '批量发卡',
                    'icon' => 'fa fa-asterisk'
                ]
            ],
            'batch'   => true,
            'filter'  => true,
            'titles'  => [
                '#', '姓名', '头像',
                [
                    'title' => '性别',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '女', 1 => '男'], 'filter_gender'
                    )
                ],
                '学生', '手机号码',
                [
                    'title' => '创建于',
                    'html' => $this->inputDateTimeRange('创建于')
                ],
                [
                    'title' => '更新于',
                    'html' => $this->inputDateTimeRange('更新于')
                ],
                [
                    'title' => '同步状态',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '未同步', 1 => '已同步'], 'filter_synced'
                    ),
                ],
                [
                    'title' => '关注状态',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '未关注', 1 => '已关注'], 'filter_subscribed'
                    )
                ],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '未启用', 1 => '已启用'], 'filter_enabled'
                    )
                ],
            ],
            'grades'  => $grades,
            'classes' => $classes,
            'title'   => '导出监护人',
        ]);
        
    }
    
}