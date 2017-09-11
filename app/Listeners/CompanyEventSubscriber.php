<?php

namespace App\Listeners;

use App\Models\Company;
use App\Models\DepartmentType;
use Illuminate\Events\Dispatcher;

class CompanyEventSubscriber {
    
    protected $company;
    protected $departmentTypeId;
    
    function __construct(Company $company) {
        $this->company = $company;
        $this->departmentTypeId = DepartmentType::whereName('运营者')->first()->id;
    }
    
    public function onDepartmentCreated($event) {

        if ($event->department->department_type_id === $this->departmentTypeId) {
            $data = [
                'name' => $event->department->name,
                'remark' => $event->department->remark,
                'department_id' => $event->department->id,
                'enabled' => $event->department->enabled
            ];
            $company = Company::whereName($event->department->name)->first();
            if ($company) {
                return $this->company->modify($data, $company->id);
            } else {
                return $this->company->store($data);
            }
        }
        return true;
        
    }
    
    public function onDepartmentUpdated($event) {
    
        if ($event->department->department_type_id === $this->departmentTypeId) {
            $data = [
                'name' => $event->department->name,
                'enabled' => $event->department->enabled,
            ];
            $departmentId = $event->department->id;
            return $this->company->modify($data, $departmentId);
        }
        return true;
    
    }
    
    public function onDepartmentDeleted($event) {
    
        if ($event->department->department_type_id === $this->departmentTypeId) {
            $departmentId = $event->department->id;
            $company = Company::whereDepartmentId($departmentId)->first();
            if ($company) {
                return $this->company->remove($company->id);
            }
        }
        return true;
    
    }
    
    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $events->listen(
            'App\Events\DepartmentCreated',
            'App\Listeners\CompanyEventSubscriber@onDepartmentCreated'
        );
        $events->listen(
            'App\Events\DepartmentUpdated',
            'App\Listeners\CompanyEventSubscriber@onDepartmentUpdated'
        );
        $events->listen(
            'App\Events\DepartmentDeleted',
            'App\Listeners\CompanyEventSubscriber@onDepartmentDeleted'
        );
        
    }
    
    
}