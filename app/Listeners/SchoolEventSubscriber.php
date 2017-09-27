<?php
namespace App\Listeners;

use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Menu;
use App\Models\MenuType;
use App\Models\School;
use Illuminate\Events\Dispatcher;

class SchoolEventSubscriber {
    
    protected $departmentTypeId, $menuTypeId;
    
    function __construct() {
        
        $this->departmentTypeId = DepartmentType::whereName('学校')->first()->id;
        $this->menuTypeId = MenuType::whereName('学校')->first()->id;
        
    }
    
    /**
     * 当部门已创建时, 更新对应学校的department_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentCreated($event) {
        
        /** @var Department $department */
        $department = $event->department;
        # 判断已创建或更新的部门的类型是否为"学校"
        if ($department->department_type_id == $this->departmentTypeId) {
            $data = ['department_id' => $department->id];
            # 更新对应"学校"的department_id
            $school = School::whereName($department->name)->first();
            
            return $school->modify($data, $school->id);
        }
        
        return true;
        
    }
    
    /**
     * 当菜单已创建时, 更新对应学校的menu_id
     *
     * @param $event
     * @return bool
     */
    public function onMenuCreated($event) {
        
        /** @var Menu $menu */
        $menu = $event->menu;
        # 判断已创建或更新的部门的类型是否为"学校"
        if ($menu->menu_type_id == $this->menuTypeId) {
            $data = ['menu_id' => $menu->id];
            # 更新对应"学校"的menu_id
            $school = School::whereName($menu->name)->first();
            
            return $school->modify($data, $school->id);
        }
        
        return true;
        
    }
    
    /**
     * 当部门所属企业发生变化时,更新对应学校的corp_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentMoved($event) {
        
        /** @var Department $department */
        $department = $event->department;
        if ($department->department_type_id == $this->departmentTypeId) {
            $school = School::whereDepartmentId($department->id)->first();
            $parentDeparment = $department->parent;
            $corpId = Corp::whereDepartmentId($parentDeparment->id);
            if ($school->corp_id != $corpId) {
                return $school->modify(['corp_id' => $corpId], $school->id);
            }
            
            return true;
        }
        
        return true;
        
    }
    
    /**
     * 当部门所属企业发生变化时,更新对应学校的corp_id
     *
     * @param $event
     * @return bool
     */
    public function onMenuMoved($event) {
        
        /** @var Menu $menu */
        $menu = $event->menu;
        if ($menu->menu_type_id == $this->menuTypeId) {
            $school = School::whereMenuId($menu->id)->first();
            $parentMenu = $menu->parent;
            $corpId = Corp::whereMenuId($parentMenu->id);
            if ($school->corp_id != $corpId) {
                return $school->modify(['corp_id' => $corpId], $school->id);
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
        $l = 'App\\Listeners\\SchoolEventSubscriber@';
        $events->listen($e . 'DepartmentCreated', $l . 'onDepartmentCreated');
        $events->listen($e . 'MenuCreated', $l . 'onMenuCreated');
        $events->listen($e . 'DepartmentMoved', $l . 'onDepartmentMoved');
        $events->listen($e . 'MenuMoved', $l . 'onMenuMoved');
        
    }
    
}