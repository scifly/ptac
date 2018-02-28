<?php
namespace App\Listeners;

use App\Jobs\ManageWechatDepartment;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Menu;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Throwable;

class DepartmentEventSubscriber {
    
    use DispatchesJobs;
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
    private function createDepartment($event, $type, $model, $belongsTo = null) {
        
        $$model = $event->{$model};
        $data = [
            'parent_id'          => isset($belongsTo) ?
                $$model->{$belongsTo}->department_id :
                $this->department->where('parent_id', null)->first()->id,
            'name'               => $$model->name,
            'remark'             => $$model->remark,
            'department_type_id' => $this->typeId($type),
            'order'              => $this->department->all()->max('order') + 1,
            'enabled'            => $$model->enabled,
        ];
        $d = $this->department->store($data, true);
        if ($d && !in_array($type, ['运营', '企业'])) {
            ManageWechatDepartment::dispatch($d, 'create');
        }

        return $d ? true : false;
        
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
            'parent_id'          => $department->parent_id,
            'name'               => $$model->name,
            'remark'             => $$model->remark,
            'department_type_id' => $this->typeId($type),
            'enabled'            => $$model->enabled,
        ];
        $d = $this->department->modify($data, $$model->department_id);
        if ($d && !in_array($type, ['运营', '企业'])) {
            ManageWechatDepartment::dispatch($d, 'update');
        }

        return $d ? true : false;
        
    }
    
    /**
     * 删除运营者对应的部门
     *
     * @param $event
     * @return bool|null
     * @throws Throwable
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
     * @throws Throwable
     */
    private function deleteDepartment($event, $model) {

        $d = $this->department->find($event->{$model}->department_id);
        if ($d && !in_array($d->departmentType->name, ['运营', '企业'])) {
            ManageWechatDepartment::dispatch($d, 'delete');
        }
        $result = $this->department->remove($event->{$model}->department_id);

        return $result;
        
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
     */
    public function onClassDeleted($event) {
        
        return $this->deleteDepartment($event, 'class');
        
    }
    
    /**
     * @param $event
     * @return bool|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function onMenuMoved($event) {
        
        /** @var Menu $menu */
        $menu = $event->menu;
        $menuType = $menu->menuType->name;
        if (in_array($menuType, ['企业', '学校'])) {
            if ($menuType == '企业') {
                $corp = $menu->corp;
                $company = $menu->parent->company;
                $department = Department::find($corp->department_id);
                $parentDepartment = Department::find($company->department_id);
            } else {
                $school = $menu->school;
                $corp = $menu->parent->corp;
                $department = Department::find($school->department_id);
                $parentDepartment = Department::find($corp->department_id);
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