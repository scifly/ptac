<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

/**
 * Class StudentIndexComposer
 * @package App\Http\ViewComposers
 */
class StudentIndexComposer {
    
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
                'import' => [
                    'id'    => 'import',
                    'label' => '批量导入',
                    'icon'  => 'fa fa-upload',
                ],
                'export' => [
                    'id'    => 'export',
                    'label' => '批量导出',
                    'icon'  => 'fa fa-download',
                ],
            ],
            'titles'         => [
                '#', '姓名', '头像',
                [
                    'title' => '性别',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '女', 1 => '男'], 'filter_gender'
                    )
                ],
                [
                    'title' => '班级',
                    'html' => $this->singleSelectList(
                        [null => '全部'] + Squad::whereIn('id', $this->classIds())->get()->pluck('name', 'id')->toArray()
                        , 'filter_class'
                    )
                ],
                '学号', '卡号',
                [
                    'title' => '住校',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '否', '是'], 'filter_oncampus'
                    )
                ],
                [
                    'title' => '生日',
                    'html' => $this->inputDateTimeRange('生日', false)
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
                ]
            ],
            'batch'          => true,
            'grades'         => $grades,
            'classes'        => $classes,
            'importTemplate' => 'files/students.xlsx',
            'title'          => '导出学籍',
            'filter'         => true
        ]);
        
    }
    
}