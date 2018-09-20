<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ExamType;
use Illuminate\Contracts\View\View;

/**
 * Class ExamIndexComposer
 * @package App\Http\ViewComposers
 */
class ExamIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $htmlExamType = $this->singleSelectList(
            array_merge(
                [null => '全部'],
                ExamType::whereSchoolId($this->schoolId())->get()->pluck('name', 'id')->toArray()
            ),
            'filter_exam_type'
        );
        $view->with([
            'titles' => [
                '#', '名称',
                ['title' => '类型', 'html' => $htmlExamType],
                '满分', '及格分数',
                [
                    'title' => '开始日期',
                    'html' => $this->inputDateTimeRange('开始日期', false)
                ],
                [
                    'title' => '结束日期',
                    'html' => $this->inputDateTimeRange('结束日期', false)
                ],
                [
                    'title' => '创建于',
                    'html' => $this->inputDateTimeRange('创建于')
                ],
                [
                    'title' => '更新于',
                    'html' => $this->inputDateTimeRange('更新于')
                ],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '已禁用', 1 => '已启用'], 'filter_enabled'
                    )
                ],
            ],
            'batch' => true,
            'filter' => true,
        ]);
        
    }
    
}