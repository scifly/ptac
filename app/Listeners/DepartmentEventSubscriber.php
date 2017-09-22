<?php

namespace App\Listeners;

use App\Models\Company;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Menu;
use App\Models\School;
use Illuminate\Events\Dispatcher;

class DepartmentEventSubscriber {
    
    protected $department, $departmentType;
    
    function __construct(Department $department, DepartmentType $departmentType) {
        
        $this->department = $department;
        $this->departmentType = $departmentType;
        
    }
    
    /**
     * 创建运营者对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onCompanyCreated($event) {
        
        return $this->createDepartment($event, '运营', 'company');
        
    }
    
    /**
     * 创建部门
     *
     * @param $event
     * @param string $type 部门类型名称
     * @param string $model 数据模型名称
     * @param string|null $belongsTo 从属的模型名称
     * @return bool
     */
    private function createDepartment($event, $type, $model, $belongsTo = NULL) {
        
        
        $$model = $event->{$model};
        $data = [
            'parent_id' => isset($belongsTo) ?
                $$model->{$belongsTo}->department_id :
                $this->department->where('parent_id', NULL)->first()->id,
            'name' => $$model->name,
            'remark' => $$model->remark,
            'department_type_id' => $this->typeId($type),
            'order' => $this->department->all()->max('order') + 1,
            'enabled' => $$model->enabled
        ];
        return $this->department->store($data, true);
        
    }
    
    /**
     * 根据部门类型名称获取部门类型ID
     *
     * @param $name
     * @return int|mixed
     */
    private function typeId($name) {
        
        return $this->departmentType->where('name', $name)->first()->id;
        
    }
    
    /**
     * 更新运营者对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onCompanyUpdated($event) {
        
        return $this->updateDepartment($event, '运营', 'company');
        
    }
    
    /**
     * 更新部门
     *
     * @param $event
     * @param string $type 部门类型名称
     * @param string $model 数据模型名称
     * @return bool
     */
    private function updateDepartment($event, $type, $model) {
        
        $$model = $event->{$model};
        $department = $event->{$model}->department;
        $data = [
            'parent_id' => $department->parent_id,
            'name' => $$model->name,
            'remark' => $$model->remark,
            'department_type_id' => $this->typeId($type),
            'enabled' => $$model->enabled
        ];
        return $this->department->modify($data, $$model->department_id);
        
    }
    
    /**
     * 删除运营者对应的部门
     *
     * @param $event
     * @return bool|null
     */
    public function onCompanyDeleted($event) {
        
        return $this->deleteDepartment($event, 'company');
        
    }
    
    /**
     * 删除部门
     *
     * @param $event
     * @param $model
     * @return bool|null
     */
    private function deleteDepartment($event, $model) {
        
        return $this->department->remove($event->{$model}->department_id);
        
    }
    
    /**
     * 创建企业对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onCorpCreated($event) {
        
        return $this->createDepartment($event, '企业', 'corp', 'company');
        
    }
    
    /**
     * 更新企业对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onCorpUpdated($event) {
        
        return $this->updateDepartment($event, '企业', 'corp');
        
    }
    
    /**
     * 删除企业对应的部门
     *
     * @param $event
     * @return bool|null
     */
    public function onCorpDeleted($event) {
        
        return $this->deleteDepartment($event, 'corp');
        
    }
    
    /**
     * 创建学校对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onSchoolCreated($event) {
        
        return $this->createDepartment($event, '学校', 'school', 'corp');
        
    }
    
    /**
     * 更新学校对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onSchoolUpdated($event) {
        
        return $this->updateDepartment($event, '学校', 'school');
        
    }
    
    /**
     * 删除学校对应的部门
     *
     * @param $event
     * @return bool|null
     */
    public function onSchoolDeleted($event) {
        
        return $this->deleteDepartment($event, 'school');
        
    }
    
    /**
     * 创建年级对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onGradeCreated($event) {
        
        return $this->createDepartment($event, '年级', 'grade', 'school');
        
    }
    
    /**
     * 更新年级对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onGradeUpdated($event) {
        
        return $this->updateDepartment($event, '年级', 'grade');
        
    }
    
    /**
     * 删除年级对应的部门
     *
     * @param $event
     * @return bool|null
     */
    public function onGradeDeleted($event) {
        
        return $this->deleteDepartment($event, 'grade');
        
    }
    
    /**
     * 创建班级对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onClassCreated($event) {
        
        return $this->createDepartment($event, '班级', 'class', 'grade');
        
    }
    
    /**
     * 更新班级对应的部门
     *
     * @param $event
     * @return bool
     */
    public function onClassUpdated($event) {
        
        return $this->updateDepartment($event, '班级', 'class');
        
    }
    
    /**
     * 删除班级对应的部门
     *
     * @param $event
     * @return bool|null
     */
    public function onClassDeleted($event) {
        
        return $this->deleteDepartment($event, 'class');
        
    }
    
    public function onMenuMoved($event) {
        
        /** @var Menu $menu */
        $menu = $event->menu;
        $menuType = $menu->menuType->name;
        if (in_array($menuType, ['企业', '学校'])) {
            if ($menuType == '企业') {
                /** @var Corp $corp */
                $corp = $menu->corp;
                /** @var Company $company */
                $company = $menu->parent->company;
                /** @var Department $department */
                $department = Department::whereId($corp->department_id)->first();
                /** @var Department $parentDepartment */
                $parentDepartment = Department::whereId($company->department_id)->first();
            } else {
                /** @var School $school */
                $school = $menu->school;
                /** @var Corp $corp */
                $corp = $menu->parent->corp;
                /** @var Department $department */
                $department = Department::whereId($school->department_id)->first();
                /** @var Department $parentDepartment */
                $parentDepartment = Department::whereId($corp->department_id)->first();
            }
            if ($department->parent_id != $parentDepartment->id) {
                return $department->modify(['parent_id' => $parentDepartment->id], $department->id);
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
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\DepartmentEventSubscriber@';
        $events->listen($e . 'CompanyCreated', $l . 'onCompanyCreated');
        $events->listen($e . 'CompanyUpdated', $l . 'onCompanyUpdated');
        $events->listen($e . 'CompanyDeleted', $l . 'onCompanyDeleted');
        
        $events->listen($e . 'CorpCreated', $l . 'onCorpCreated');
        $events->listen($e . 'CorpUpdated', $l . 'onCorpUpdated');
        $events->listen($e . 'CorpDeleted', $l . 'onCorpDeleted');
        
        $events->listen($e . 'SchoolCreated', $l . 'onSchoolCreated');
        $events->listen($e . 'SchoolUpdated', $l . 'onSchoolUpdated');
        $events->listen($e . 'SchoolDeleted', $l . 'onSchoolDeleted');
        
        $events->listen($e . 'GradeCreated', $l . 'onGradeCreated');
        $events->listen($e . 'GradeUpdated', $l . 'onGradeUpdated');
        $events->listen($e . 'GradeDeleted', $l . 'onGradeDeleted');
        
        $events->listen($e . 'ClassCreated', $l . 'onClassCreated');
        $events->listen($e . 'ClassUpdated', $l . 'onClassUpdated');
        $events->listen($e . 'ClassDeleted', $l . 'onClassDeleted');
        
        $events->listen($e . 'MenuMoved', $l . 'onMenuMoved');
        
    }
    
}