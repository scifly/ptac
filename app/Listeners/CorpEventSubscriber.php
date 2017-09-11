<?php

namespace App\Listeners;

use App\Models\Company;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentType;
use Illuminate\Events\Dispatcher;

class CorpEventSubscriber {
    
    protected $corp;
    protected $company;
    protected $department;
    protected $departmentTypeId;
    
    function __construct(Corp $corp, Company $company, Department $department) {
        $this->corp = $corp;
        $this->company = $company;
        $this->department = $department;
        //$this->departmentTypeId = DepartmentType::whereName('企业')->first()->id;
    }
    
    public function onDepartmentCreated($event) {

        if ($event->department->department_id === $this->departmentTypeId) {
            $parentDepartmentId = $event->department->parent_id;
            $parentCompanyId = Company::whereDepartmentId($parentDepartmentId)->first()->id;
            $data = [
                'name' => $event->department->name,
                'company_id' => $parentCompanyId,
                'remark' => $event->department->remark,
                'deparment_id' => $event->department->id,
                'enabled' => $event->department->enabled,
            ];
            $corp = Corp::whereName($event->department->name)->first();
            if ($corp) {
                return $corp->modify($data, $corp->id);
            } else {
                $data['corpid'] = str_repeat('x', 18);
                $data['corpsecret'] = str_repeat('x', 64);
                return $this->corp->store($data);
            }
        }
        return true;
        
    }
    
    public function onDepartmentUpdated($event) {
    
        if ($event->department->department_id === $this->departmentTypeId) {
            $parentDepartmentId = $event->department->parent_id;
            $parentCompanyId = Company::whereDepartmentId($parentDepartmentId)->first()->id;
            $data = [
                'name' => $event->department->name,
                'company_id' => $parentCompanyId,
                'remark' => $event->department->remark,
                'deparment_id' => $event->department->id,
                'enabled' => $event->department->enabled,
            ];
            $corp = Corp::whereName($event->department->name)->first();
            if ($corp) {
                return $corp->modify($data, $corp->id);
            } else {
                $data['corpid'] = str_repeat('x', 18);
                $data['corpsecret'] = str_repeat('x', 64);
                return $this->corp->store($data);
            }
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
            'App\Listeners\CorpEventSubscriber@onDepartmentCreated'
        );
        $events->listen(
            'App\Events\DepartmentUpdated',
            'App\Listeners\CorpEventSubscriber@onDepartmentUpdated'
        );
        $events->listen(
            'App\Events\DepartmentDeleted',
            'App\Listeners\CorpEventSubscriber@onDepartmentDeleted'
        );
        
    }
    
    
}