<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Student;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;
use ReflectionException;

/**
 * Class ConsumptionComposer
 * @package App\Http\ViewComposers
 */
class ConsumptionComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     * @throws ReflectionException
     * @throws Exception
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'buttons' => [
                    'stat'   => [
                        'id'    => 'stat',
                        'label' => '统计',
                        'icon'  => 'fa fa-bar-chart',
                    ],
                    'export' => [
                        'id'    => 'export',
                        'label' => '批量导出',
                        'icon'  => 'fa fa-download',
                    ],
                ],
                'titles'  => ['#', '学生', '消费地点', '消费机ID', '类型', '金额', '时间'],
            ];
        } else {
            $ranges = [
                1 => '学生',
                2 => '班级',
                3 => '年级',
            ];
            $students = [];
            $values = Student::whereIn('id', $this->contactIds('student'))->get();
            foreach ($values as $v) {
                $students[$v->id] = $v->user->realname . '(' . $v->squad->grade->name . ' / ' . $v->squad->name . ')';
            }
            $classes = Squad::whereIn('id', $this->classIds())->pluck('name', 'id')->toArray();
            $grades = Grade::whereIn('id', $this->gradeIds())->pluck('name', 'id')->toArray();
            $data = [
                'ranges'   => $ranges,
                'students' => $students,
                'classes'  => $classes,
                'grades'   => $grades,
            ];
        }
        
        $view->with($data);
        
    }
    
}