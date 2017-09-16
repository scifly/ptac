<?php

namespace App\Listeners;

use App\Models\Company;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Menu;
use App\Models\MenuType;
use Illuminate\Events\Dispatcher;

class CompanyEventSubscriber {
    
    protected $departmentTypeId, $menuTypeId;
    
    function __construct() {
        
        $this->departmentTypeId = DepartmentType::whereName('运营')->first()->id;
        $this->menuTypeId = MenuType::whereName('运营')->first()->id;
        
    }
    
    /**
     * 当部门已创建时, 更新对应运营者的department_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentCreated($event) {
    
        /** @var Department $department */
        $department = $event->department;
        # 判断已创建或更新的部门的类型是否为"运营者"
        if ($department->department_type_id == $this->departmentTypeId) {
            $data = ['department_id' => $department->id];
            # 更新部门对应"运营者"的department_id (公司名称是唯一的)
            $company = Company::whereName($department->name)->first();
            return $company->modify($data, $company->id);
        }
        return true;
    
    }
    
    /**
     * 当菜单已创建时, 更新对应运营者的menu_id
     *
     * @param $event
     * @return bool
     */
    public function onMenuCreated($event) {
        
        /** @var Menu $menu */
        $menu = $event->menu;
        # 判断已创建的菜单类型是否为"运营者"
        if ($menu->menu_type_id == $this->menuTypeId) {
            $data = ['menu_id' => $menu->id];
            # 更新部门对应"运营者"的department_id (公司名称是唯一的)
            $company = Company::whereName($menu->name)->first();
            return $company->modify($data, $company->id);
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
        $l = 'App\\Listeners\\CompanyEventSubscriber@';
        $events->listen($e . 'DepartmentCreated', $l . 'onDepartmentCreated');
        $events->listen($e . 'MenuCreated', $l . 'onMenuCreated');
        
    }
    
}