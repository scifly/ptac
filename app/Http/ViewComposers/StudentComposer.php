<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Card;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class StudentComposer
 * @package App\Http\ViewComposers
 */
class StudentComposer {
    
    use ModelTrait;
    
    protected $student;
    
    /**
     * StudentComposer constructor.
     * @param Student $student
     */
    function __construct(Student $student) { $this->student = $student; }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $grades = Grade::whereIn('id', $this->gradeIds())
                    ->where('enabled', 1)
                    ->pluck('name', 'id')
                    ->toArray();
                $classes = Squad::whereGradeId(array_key_first($grades))
                    ->where('enabled', 1)
                    ->pluck('name', 'id')
                    ->toArray();
                $optionAll = [null => '全部'];
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
                        'issue'  => [
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
                    'titles'         => [
                        '#', '姓名', '头像',
                        [
                            'title' => '性别',
                            'html'  => $this->singleSelectList(
                                $optionAll + [0 => '女', 1 => '男'], 'filter_gender'
                            ),
                        ],
                        [
                            'title' => '班级',
                            'html'  => $this->singleSelectList(
                                $optionAll + Squad::whereIn('id', $this->classIds())
                                    ->pluck('name', 'id')->toArray()
                                , 'filter_class'
                            ),
                        ],
                        '学号',
                        [
                            'title' => '住校',
                            'html'  => $this->singleSelectList(
                                $optionAll + [0 => '否', 1 => '是'], 'filter_oncampus'
                            ),
                        ],
                        [
                            'title' => '生日',
                            'html'  => $this->inputDateTimeRange('生日', false),
                        ],
                        [
                            'title' => '创建于',
                            'html'  => $this->inputDateTimeRange('创建于'),
                        ],
                        [
                            'title' => '更新于',
                            'html'  => $this->inputDateTimeRange('更新于'),
                        ],
                        [
                            'title' => '状态 . 操作',
                            'html'  => $this->singleSelectList(
                                $optionAll + [0 => '已禁用', 1 => '已启用'], 'filter_enabled'
                            ),
                        ],
                    ],
                    'batch'          => true,
                    'grades'         => $grades,
                    'classes'        => $classes,
                    'importTemplate' => 'files/students.xlsx',
                    'title'          => '导出学籍',
                    'filter'         => true,
                ];
                break;
            case 'issue':
                $classes = Squad::whereIn('id', $this->classIds())
                    ->get()->pluck('name', 'id')->toArray();
                $titles = <<<HTML
<th>#</th>
<th class="text-center">姓名</th>
<th class="text-center">学号</th>
<th>卡号</th>
HTML;
                $data = [
                    'prompt'  => '学生列表',
                    'formId'  => 'formStudent',
                    'classes' => [0 => '(请选择一个班级)'] + $classes,
                    'titles'  => $titles,
                    'columns' => 4,
                ];
                break;
            case 'grant':
                $data = (new Card)->compose('Student');
                break;
            default:
                $data = array_combine(
                    ['student', 'grades', 'classes', 'mobiles'],
                    $this->student->compose()
                );
                break;
        }
        
        $view->with($data);
        
    }
    
}