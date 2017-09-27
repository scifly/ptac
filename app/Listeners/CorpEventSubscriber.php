<?php
namespace App\Listeners;

use App\Models\Company;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Menu;
use App\Models\MenuType;
use Illuminate\Events\Dispatcher;

class CorpEventSubscriber {
    
    protected $departmentTypeId, $menuTypeId;
    
    function __construct() {
        
        $this->departmentTypeId = DepartmentType::whereName('企业')->first()->id;
        $this->menuTypeId = MenuType::whereName('企业')->first()->id;
        
    }
    
    /**
     * 当部门已创建时, 更新对应企业的department_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentCreated($event) {
        
        /** @var Department $department */
        $department = $event->department;
        # 判断已创建或更新的部门的类型是否为"企业"
        if ($department->department_type_id == $this->departmentTypeId) {
            $data = ['department_id' => $department->id];
            # 更新对应企业的department_id (企业名称是唯一的)
            $corp = Corp::whereName($department->name)->first();
            
            return $corp->modify($data, $corp->id);
        }
        
        return true;
        
    }
    
    /**
     * 当菜单已创建时, 更新对应企业的menu_id
     *
     * @param $event
     * @return bool
     */
    public function onMenuCreated($event) {
        
        /** @var Menu $menu */
        $menu = $event->menu;
        # 判断已创建或更新的部门的类型是否为"企业"
        if ($menu->menu_type_id == $this->menuTypeId) {
            $data = ['menu_id' => $menu->id];
            # 更新对应企业的menu_id (企业名称是唯一的)
            $corp = Corp::whereName($menu->name)->first();
            
            return $corp->modify($data, $corp->id);
        }
        
        return true;
        
    }
    
    /**
     * 当部门所属公司发生变化时,更新对应企业的company_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentMoved($event) {
        
        /** @var Department $department */
        $department = $event->department;
        if ($department->department_type_id == $this->departmentTypeId) {
            $corp = Corp::whereDepartmentId($department->id)->first();
            $parentDepartment = $department->parent;
            $companyId = Company::whereDepartmentId($parentDepartment->id)->first()->id;
            if ($corp->company_id != $companyId) {
                return $corp->modify(['company_id' => $companyId], $corp->id);
            }
            
            return true;
        }
        
        return true;
        
    }
    
    /**
     * 当部门所属公司发生变化时,更新对应企业的company_id
     *
     * @param $event
     * @return bool
     */
    public function onMenuMoved($event) {
        
        /** @var Menu $menu */
        $menu = $event->menu;
        if ($menu->menu_type_id == $this->menuTypeId) {
            $corp = Corp::whereMenuId($menu->id)->first();
            $parentMenu = $menu->parent;
            $companyId = Company::whereMenuId($parentMenu->id)->first()->id;
            if ($corp->company_id != $companyId) {
                return $corp->modify(['company_id' => $companyId], $corp->id);
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
        $l = 'App\\Listeners\\CorpEventSubscriber@';
        $events->listen($e . 'DepartmentCreated', $l . 'onDepartmentCreated');
        $events->listen($e . 'MenuCreated', $l . 'onMenuCreated');
        $events->listen($e . 'DepartmentMoved', $l . 'onDepartmentMoved');
        $events->listen($e . 'MenuMoved', $l . 'onMenuMoved');
        
    }
    
}