<?php

namespace App\Listeners;

use App\Models\Company;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use Illuminate\Events\Dispatcher;

class DepartmentEventSubscriber {
    
    protected $department, $company, $corp, $school, $grade, $class, $departmentType;
    
    function __construct(
        Department $department,
        Company $company,
        Corp $corp,
        School $school,
        Grade $grade,
        Squad $class,
        DepartmentType $departmentType
    ) { 
        $this->department = $department;
        $this->company = $company;
        $this->corp = $corp;
        $this->school = $school;
        $this->grade = $grade;
        $this->class = $class;
        $this->departmentType = $departmentType;
    }
    
    public function onCompanyCreated($event) {
        
        $departmentTypeId = $this->departmentType->where('name', '运营者')->first()->id;
        $data = [
            'parent_id' => NULL,
            'name' => $event->company->name,
            'remark' => $event->company->remark,
            'department_type_id' => $departmentTypeId,
            'enabled' => 1
        ];
        return $this->department->store($data, true);
        
    }
    
    public function onCompanyUpdated($event) {
    
        $departmentTypeId = $this->departmentType->where('name', '运营者')->first()->id;
        $data = [
            'parent_id' => NULL,
            'name' => $event->company->name,
            'remark' => $event->company->remark,
            'department_type_id' => $departmentTypeId,
            'enabled' => $event->company->enabled
        ];
        $departmentId = $event->company->department_id;
        return $this->department->modify($data, $departmentId);
        
    }
    
    public function onCompanyDeleted($event) {
        
        $departmentId = $event->company->department_id;
        return $this->department->remove($departmentId);
        
    }
    
    public function onCorpCreated($event) {
    
        $departmentTypeId = $this->departmentType->where('name', '企业')->first()->id;
        $data = [
            'parent_id' => $event->corp->company->department_id,
            'name' => $event->corp->name,
            'remark' => $event->corp->remark,
            'department_type_id' => $departmentTypeId,
            'enabled' => 1
        ];
        return $this->department->store($data, true);
        
    }
    
    public function onCorpUpdated($event) {
    
        $departmentTypeId = $this->departmentType->where('name', '企业')->first()->id;
        $data = [
            'name' => $event->corp->name,
            'remark' => $event->corp->remark,
            'parent_id' => $event->corp->company->department_id,
            'department_type_id' => $departmentTypeId,
            'enabled' => $event->corp->enabled,
        ];
        $departmentId = $event->corp->department_id;
        return $this->department->modify($data, $departmentId);
        
    }
    
    public function onCorpDeleted($event) {
        
        $departmentId = $event->corp->department_id;
        return $this->department->remove($departmentId);
        
    }
    
    public function onSchoolCreated($event) {

        $parentId = $event->school->corp->department_id;
        $departmentTypeId = $this->departmentType->where('name', '学校')->first()->id;
        $data = [
            'parent_id' => $parentId,
            'department_type_id' => $departmentTypeId,
            'name' => $event->school->name,
            'remark' => $event->school->remark,
            'enabled' => 1
        ];
        return $this->department->store($data, true);
        
    }
    
    public function onSchoolUpdated($event) {

        $parentId = $event->school->corp->department_id;
        $departmentTypeId = $this->departmentType->where('name', '学校')->first()->id;
        $data = [
            'parent_id' => $parentId,
            'department_type_id' => $departmentTypeId,
            'name' => $event->school->name,
            'remark' => $event->school->remark,
            'enabled' => $event->school->enabled
        ];
        $departmentId = $event->school->department_id;
        return $this->department->modify($data, $departmentId);
        
    }
    
    public function onSchoolDeleted($event) {

        $departmentId = $event->school->department_id;
        return $this->department->remove($departmentId);
        
    }
    
    public function onGradeCreated($event) {}
    public function onGradeUpdated($event) {}
    public function onGradeDeleted($event) {}
    public function onClassCreated($event) {}
    public function onClassUpdated($event) {}
    public function onClassDeleted($event) {}
    
    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $events->listen('App\Events\CompanyCreated', 'App\Listeners\DepartmentEventSubscriber@onCompanyCreated');
        $events->listen('App\Events\CompanyUpdated', 'App\Listeners\DepartmentEventSubscriber@onCompanyUpdated');
        $events->listen('App\Events\CompanyDeleted', 'App\Listeners\DepartmentEventSubscriber@onCompanyDeleted');
        $events->listen('App\Events\CorpCreated', 'App\Listeners\DepartmentEventSubscriber@onCorpCreated');
        $events->listen('App\Events\CorpUpdated', 'App\Listeners\DepartmentEventSubscriber@onCorpUpdated');
        $events->listen('App\Events\CorpDeleted', 'App\Listeners\DepartmentEventSubscriber@onCorpDeleted');
        $events->listen('App\Events\SchoolCreated', 'App\Listeners\DepartmentEventSubscriber@onSchoolCreated');
        $events->listen('App\Events\SchoolUpdated', 'App\Listeners\DepartmentEventSubscriber@onSchoolUpdated');
        $events->listen('App\Events\SchoolDeleted', 'App\Listeners\DepartmentEventSubscriber@onSchoolDeleted');
        $events->listen('App\Events\GradeCreated', 'App\Listeners\DepartmentEventSubscriber@onGradeCreated');
        $events->listen('App\Events\GradeUpdated', 'App\Listeners\DepartmentEventSubscriber@onGradeUpdated');
        $events->listen('App\Events\GradeDeleted', 'App\Listeners\DepartmentEventSubscriber@onGradeDeleted');
        $events->listen('App\Events\ClassCreated', 'App\Listeners\DepartmentEventSubscriber@onClassCreated');
        $events->listen('App\Events\ClassUpdated', 'App\Listeners\DepartmentEventSubscriber@onClassUpdated');
        $events->listen('App\Events\ClassDeleted', 'App\Listeners\DepartmentEventSubscriber@onClassDeleted');
    
    }
    
    
}