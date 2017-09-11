<?php

namespace App\Listeners;

use App\Models\DepartmentType;
use App\Models\school;
use Illuminate\Events\Dispatcher;

class SchoolEventSubscriber {
    
    protected $school;
    protected $departmentTypeId;
    
    function __construct(School $school) {
        
        $this->school = $school;
        $this->departmentTypeId = DepartmentType::whereName('学校')->first()->id;
        
    }
    
    public function onDepartmentCreated($event) {
        
        if ($event->department->department_type_id === $this->departmentTypeId) {
            $data = ['department_id' => $event->department->id];
            $schoolName = $event->department->name;
            $id = $this->school->where('name', $schoolName)->first()->id;
            return $this->school->modify($data, $id);
        }
        return true;
        
    }
    
    public function onDepartmentUpdated($event) {
        
        if ($event->department->department_type_id === $this->departmentTypeId) {
            $data = ['department_id' => $event->department->id];
            $schoolName = $event->department->name;
            $id = $this->school->where('name', $schoolName)->first()->id;
            return $this->school->modify($data, $id);
        }
        return true;
    
    }
    
    public function onDepartmentDeleted($event) {
    
    }
    
    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $events->listen(
            'App\Events\DepartmentCreated',
            'App\Listeners\SchoolEventSubscriber@onDepartmentCreated'
        );
        $events->listen(
            'App\Events\DepartmentUpdated',
            'App\Listeners\SchoolEventSubscriber@onDepartmentUpdated'
        );
        $events->listen(
            'App\Events\DepartmentDeleted',
            'App\Listeners\SchoolEventSubscriber@onDepartmentDeleted'
        );
        
    }
    
    
}