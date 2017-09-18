<?php

namespace App\Listeners;

use App\Models\Department;
use App\Models\School;
use App\Models\DepartmentType;
use App\Models\Grade;
use Illuminate\Events\Dispatcher;

class GradeEventSubscriber {
    
    protected $department;
    protected $departmentTypeId;
    
    function __construct(Department $department) {
        
        $this->department = $department;
        $this->departmentTypeId = DepartmentType::whereName('年级')->first()->id;
        
    }
    
    /**
     * 当部门已创建时, 更新对应年级的department_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentCreated($event) {
        
        $abc = 'abc';
        $department = $event->department;
        # 判断已创建或更新的部门的类型是否为"年级"
        if ($department->department_type_id == $this->departmentTypeId) {
            $parentSchoolId = $this->department->getSchoolId($department->id);
            # 更新对应年级的department_id (年级名称 + 学校ID)
            $data = ['department_id' => $department->id];
            $grade = Grade::whereName($department->name)
                ->where('school_id', $parentSchoolId)
                ->first();
            return $grade->modify($data, $grade->id);
        }
        return true;
        
    }
    
    /**
     * 当部门所属学校发生变化时,更新对应年级的school_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentMoved($event) {
        
        /** @var Department $department */
        $department = $event->department;
        if ($department->department_type_id == $this->departmentTypeId) {
            $grade = Grade::whereDepartmentId($department->id)->first();
            $schoolId = $this->department->getSchoolId($department->id);
            if ($grade->school_id != $schoolId) {
                return $grade->modify(['school_id' => $schoolId], $grade->id);
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
        $l = 'App\\Listeners\\GradeEventSubscriber@';
        $events->listen($e . 'DepartmentCreated', $l . 'onDepartmentCreated');
        $events->listen($e . 'DepartmentMoved', $l . 'onDepartmentMoved');
        
    }
    
}