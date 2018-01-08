<?php
namespace App\Listeners;

use App\Events\MenuCreated;
use App\Jobs\ManageCreateMenu;
use App\Models\Company;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Icon;
use App\Models\Menu;
use App\Models\MenuType;
use App\Models\School;
use Illuminate\Events\Dispatcher;

class MenuEventSubscriber {
    
    protected $menu, $menuType, $icons;
    
    function __construct(Menu $menu, MenuType $menuType) {
        
        $this->menu = $menu;
        $this->menuType = $menuType;
        $this->icons = [
            'company' => Icon::whereName('fa fa-building')->first()->id,
            'corp'    => Icon::whereName('fa fa-weixin')->first()->id,
            'school'  => Icon::whereName('fa fa-university')->first()->id,
        ];
        
    }
    
    /**
     * 创建运营者对应的菜单
     *
     * @param $event
     * @return bool|mixed
     */
    public function onCompanyCreated($event) {
        
        return $this->createMenu($event, '运营', 'company');
        
    }
    
    /**
     * 创建菜单
     *
     * @param $event
     * @param $type
     * @param $model
     * @param null $belongsTo
     * @return bool|mixed
     */
    private function createMenu($event, $type, $model, $belongsTo = null) {
        
        $$model = $event->{$model};
        $data = [
            'parent_id'    => isset($belongsTo) ?
                $$model->{$belongsTo}->menu_id :
                $this->menu->where('parent_id', null)->first()->id,
            'name'         => $$model->name,
            'remark'       => $$model->remark,
            'menu_type_id' => $this->typeId($type),
            'icon_id'      => $this->icons[$model],
            'position'     => $this->menu->all()->max('position') + 1,
            'enabled'      => $$model->enabled,
        ];
        if($type == '学校'){
            $parentMenu = $this->menu->create($data);
            ManageCreateMenu::dispatch($parentMenu);
            if ($parentMenu) {
                event(new MenuCreated($parentMenu));
                return true;
            }
        }
        return $this->menu->preserve($data, true);
        
    }
    
    private function typeId($name) {
        
        return MenuType::whereName($name)->first()->id;
        
    }
    
    /**
     * 更新运营者对应的菜单
     *
     * @param $event
     * @return bool
     */
    public function onCompanyUpdated($event) {
        
        return $this->updateMenu($event, '运营', 'company');
        
    }
    
    /**
     * 更新菜单
     *
     * @param $event
     * @param $type
     * @param $model
     * @return bool
     */
    private function updateMenu($event, $type, $model) {
        
        $$model = $event->{$model};
        /** @var Menu $menu */
        $menu = $event->{$model}->menu;
        $data = [
            'parent_id'    => $menu->parent_id,
            'name'         => $$model->name,
            'remark'       => $$model->remark,
            'menu_type_id' => $this->typeId($type),
            'icon_id'      => $this->icons[$model],
            'enabled'      => $$model->enabled,
        ];
        
        return $this->menu->alter($data, $$model->menu_id);
        
    }
    
    /**
     * 删除运营者对应的菜单
     *
     * @param $event
     * @return bool
     */
    public function onCompanyDeleted($event) {
        
        return $this->deleteMenu($event, 'company');
        
    }
    
    /**
     * 删除菜单
     *
     * @param $event
     * @param $model
     * @return bool
     */
    private function deleteMenu($event, $model) {
        
        return $this->menu->purge($event->{$model}->menu_id);
        
    }
    
    /**
     * 创建企业对应的菜单
     *
     * @param $event
     * @return bool|mixed
     */
    public function onCorpCreated($event) {
        
        return $this->createMenu($event, '企业', 'corp', 'company');
        
    }
    
    /**
     * 更新企业对应的菜单
     *
     * @param $event
     * @return bool
     */
    public function onCorpUpdated($event) {
        
        return $this->UpdateMenu($event, '企业', 'corp');
        
    }
    
    /**
     * 删除企业对应的菜单
     *
     * @param $event
     * @return bool
     */
    public function onCorpDeleted($event) {
        
        return $this->deleteMenu($event, 'corp');
        
    }
    
    /**
     * 创建学校对应的菜单
     *
     * @param $event
     * @return bool|mixed
     */
    public function onSchoolCreated($event) {
        
        return $this->createMenu($event, '学校', 'school', 'corp');
        
    }
    
    /**
     * 更新学校对应的菜单
     *
     * @param $event
     * @return bool
     */
    public function onSchoolUpdated($event) {
        
        return $this->updateMenu($event, '学校', 'school');
        
    }
    
    /**
     * 删除学校对应的菜单
     *
     * @param $event
     * @return bool
     */
    public function onSchoolDeleted($event) {
        
        return $this->deleteMenu($event, 'school');
        
    }
    
    /**
     * 当企业或学校所属上级部门发生变化时, 更新对应菜单的parent_id
     *
     * @param $event
     * @return bool
     */
    public function onDepartmentMoved($event) {
        
        /** @var Department $department */
        $department = $event->department;
        $departmentType = $department->departmentType->name;
        if (in_array($departmentType, ['企业', '学校'])) {
            if ($departmentType == '企业') {
                $corp = $department->corp;
                $company = $department->parent->company;
                $menu = Menu::find($corp->menu_id);
                $parentMenu = Menu::find($company->menu_id);
            } else {
                $school = $department->school;
                $corp = $department->parent->corp;
                $menu = Menu::find($school->menu_id);
                $parentMenu = Menu::find($corp->menu_id);
            }
            if ($menu->parent_id != $parentMenu->id) {
                return $menu->alter(['parent_id' => $parentMenu->id], $menu->id);
            }
        }
        
        return true;
        
    }
    
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\MenuEventSubscriber@';
        $events->listen($e . 'CompanyCreated', $l . 'onCompanyCreated');
        $events->listen($e . 'CompanyUpdated', $l . 'onCompanyUpdated');
        $events->listen($e . 'CompanyDeleted', $l . 'onCompanyDeleted');
        $events->listen($e . 'CorpCreated', $l . 'onCorpCreated');
        $events->listen($e . 'CorpUpdated', $l . 'onCorpUpdated');
        $events->listen($e . 'CorpDeleted', $l . 'onCorpDeleted');
        $events->listen($e . 'SchoolCreated', $l . 'onSchoolCreated');
        $events->listen($e . 'SchoolUpdated', $l . 'onSchoolUpdated');
        $events->listen($e . 'SchoolDeleted', $l . 'onSchoolDeleted');
        $events->listen($e . 'DepartmentMoved', $l . 'onDepartmentMoved');
        
    }
    
}