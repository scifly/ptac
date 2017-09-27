<?php
namespace App\Listeners;

use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Squad;
use Illuminate\Events\Dispatcher;

class ClassEventSubscriber {
    
    protected $class;
    protected $department;
    protected $departmentTypeId;
    
    function __construct(Squad $class, Department $department) {
        
        $this->class = $class;
        $this->department = $department;
        $this->departmentTypeId = DepartmentType::whereName('班级')->first()->id;
        
    }
    
    /**
     * 当部门已创建时, 更新对应班级的department_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentCreated($event) {
        
        $department = $event->department;
        # 判断已创建或更新的部门的类型是否为"班级"
        if ($department->department_type_id == $this->departmentTypeId) {
            $parentGradeId = $this->department->getGradeId($department->id);
            $data = ['department_id' => $department->id];
            # 更新对应班级的department_id (班级名称 + 年级ID)
            $class = Squad::whereName($department->name)
                ->where('grade_id', $parentGradeId)
                ->first();
            return $this->class->modify($data, $class->id);
        }
        return true;
        
    }
    
    /**
     * 当部门所属年级发生变化时,更新对应班级的grade_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentMoved($event) {
        
        /** @var Department $department */
        $department = $event->department;
        if ($department->department_type_id == $this->departmentTypeId) {
            $class = Squad::whereDepartmentId($department->id)->first();
            $gradeId = $this->department->getGradeId($department->id);
            if ($class->grade_id != $gradeId) {
                return $class->modify(['grade_id' => $gradeId], $class->id);
            }
            return true;
        }
        return true;
        
    }
    
    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\ClassEventSubscriber@';
        $events->listen($e . 'DepartmentCreated', $l . 'onDepartmentCreated');
        $events->listen($e . 'DepartmentMoved', $l . 'onDepartmentMoved');
        
    }
    
}