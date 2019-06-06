<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Card;
use App\Models\Custodian;
use App\Models\Department;
use App\Models\Educator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class EducatorComposer
 * @package App\Http\ViewComposers
 */
class EducatorComposer {
    
    use ModelTrait;
    
    protected $educator, $custodian;
    
    /**
     * EducatorComposer constructor.
     * @param Educator $educator
     * @param Custodian $custodian
     */
    function __construct(Educator $educator, Custodian $custodian) {
        
        $this->educator = $educator;
        $this->custodian = $custodian;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $departments = Department::whereIn('id', $this->departmentIds(Auth::id()))
                    ->pluck('name', 'id')->toArray();
                $data = [
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
                        'issue' => [
                            'id'    => 'issue',
                            'label' => '发卡',
                            'icon'  => 'fa fa-credit-card',
                        ],
                        'grant' => [
                            'id'    => 'grant',
                            'label' => '一卡通授权',
                            'icon'  => 'fa fa-credit-card',
                        ],
                    ],
                    'batch'          => true,
                    'filter'         => true,
                    'titles'        => [
                        '#', '姓名', '头像',
                        [
                            'title' => '性别',
                            'html' => $this->singleSelectList(
                                [null => '全部', 0 => '女', 1 => '男'], 'filter_gender'
                            )
                        ],
                        '职务', '手机号码',
                        [
                            'title' => '创建于',
                            'html'  => $this->inputDateTimeRange('创建于')
                        ],
                        [
                            'title' => '更新于',
                            'html'  => $this->inputDateTimeRange('更新于')
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
                    'departments'    => $departments,
                    'importTemplate' => 'files/educators.xlsx',
                    'title'          => '导出教职员工',
                ];
                break;
            case 'issue':
                $departments = Department::whereIn('id', $this->departmentIds())
                    ->get()->pluck('name', 'id')->toArray();
                $titles = <<<HTML
<th>#</th>
<th class="text-center">姓名</th>
<th class="text-center">员工编号/用户名</th>
<th>卡号</th>
HTML;
                $data = [
                    'prompt' => '教师列表',
                    'formId' => 'formEducator',
                    'classes' => [0 => '(请选择一个部门)'] + $departments,
                    'titles' => $titles,
                    'columns' => 4
                ];
                break;
            case 'grant':
                $data = (new Card)->compose('Educator');
                break;
            default:
                $data = array_combine(
                    [
                        'educator', 'squads', 'subjects', 'groups', 'selectedDepartmentIds',
                        'selectedDepartments', 'mobiles'
                    ],
                    $this->educator->compose()
                );
                break;
        }
        
        $view->with($data);
        
    }
    
}