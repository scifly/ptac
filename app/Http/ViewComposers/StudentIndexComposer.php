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
                '#', '姓名', '头像', '性别', '班级', '学号', '卡号', '住校',
                '手机', '生日', '创建于', '更新于', '状态 . 操作',
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