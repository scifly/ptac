<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class EducatorIndexComposer
 * @package App\Http\ViewComposers
 */
class EducatorIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $departments = Department::whereIn('id', $this->departmentIds(Auth::id()))
            ->pluck('name', 'id')->toArray();
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
            'batch'          => true,
            'titles'         => ['#', '姓名', '头像', '性别', '创建于', '更新于', '状态 . 操作'],
            'departments'    => $departments,
            'importTemplate' => 'files/educators.xlsx',
            'title'          => '导出教职员工',
        ]);
        
    }
    
}