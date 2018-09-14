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
            'filter'         => true,
            'titles'        => [
                '#', '姓名', '头像',
                [
                    'title' => '性别',
                    'html' => $this->singleSelectList(
                        [-1 => '全部', 0 => '女', 1 => '男'], 'filter_gender'
                    )
                ],
                '职务', '创建于', '更新于', '同步状态',
                [
                    'title' => '同步状态',
                    'html' => $this->singleSelectList(
                        [-1 => '全部', 0 => '未同步', 1 => '已同步'], 'filter_synced'
                    ),
                ],
                [
                    'title' => '关注状态',
                    'html' => $this->singleSelectList(
                        [-1 => '全部', 0 => '未关注', 1 => '已关注'], 'filter_subscribed'
                    )
                ],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        [-1 => '全部', 0 => '未启用', 1 => '已启用'], 'filter_enabled'
                    )
                ],
            ],
            'departments'    => $departments,
            'importTemplate' => 'files/educators.xlsx',
            'title'          => '导出教职员工',
        ]);
        
    }
    
}