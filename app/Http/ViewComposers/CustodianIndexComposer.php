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
            'buttons' => [
                'export' => [
                    'id'    => 'export',
                    'label' => '批量导出',
                    'icon'  => 'fa fa-download',
                ],
            ],
            'batch'   => true,
            'filter'  => true,
            'titles'  => ['#', '姓名', '头像', '性别', '学生', '邮箱', '手机号码', '创建于', '更新于', '状态 . 操作'],
            'grades'  => $grades,
            'classes' => $classes,
            'title'   => '导出监护人',
        ]);
        
    }
    
}