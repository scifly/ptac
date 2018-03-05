<?php
namespace App\Helpers;

use App\Models\Action;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use App\Policies\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use ReflectionClass;

trait ModelTrait {
    
    /**
     * 判断指定记录能否被删除
     *
     * @param Model $model
     * @return bool
     * @throws \ReflectionException
     */
    function removable(Model $model) {
        
        $relations = [];
        $class = get_class($model);
        $reflectionClass = new ReflectionClass($class);
        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->isUserDefined() && $method->isPublic() && $method->class == $class) {
                $doc = $method->getDocComment();
                // if ($doc && stripos($doc, 'Relations\Has') !== false) {
                if ($doc && stripos($doc, 'Has') !== false) {
                    $relations[] = $method->getName();
                }
            }
        }
        foreach ($relations as $relation) {
            if(count($model->{$relation})){
                return false;
            }
        }
        return true;
        
    }

    /**
     * 获取当前控制器包含的方法所对应的路由对象数组
     *
     * @return array
     */
    static function uris() {

        $controller = class_basename(Request::route()->controller);
        $routes = Action::whereController($controller)
            ->where('route', '<>', null)
            ->pluck('route', 'method')
            ->toArray();
        $uris = [];
        foreach ($routes as $key => $value) {
            $uris[$key] = new Route($value);
        }
        
        return $uris;

    }
    
    /**
     * 根据当前菜单Id及用户角色返回学校Id
     *
     * @return int|mixed
     */
    function schoolId() {
    
        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
            case '企业':
                $menu = new Menu();
                $schoolMenuId = $menu->menuId(session('menuId'));
                unset($menu);
                return $schoolMenuId ? School::whereMenuId($schoolMenuId)->first()->id : 0;
            case '学校':
                $departmentId = $user->topDeptId();
                return School::whereDepartmentId($departmentId)->first()->id;
            default:
                return $user->educator->school_id;
        }
        
    }
    
    /**
     * 获取对当前用户可见的联系人id
     *
     * @param string $type - 联系人类型: custodian, student, educator
     * @return array
     */
    function contactIds($type) {
        
        $user = Auth::user();
        $role = $user->group->name;
        $method = $type . 'Ids';
        if (method_exists($this, $method)) {
            if (in_array($role, Constant::SUPER_ROLES)) {
                $contactIds = $this->$method(
                    School::find($schoolId = $this->schoolId())->department_id
                );
            } else {
                $departments = $user->departments;
                $contactIds = [];
                foreach ($departments as $d) {
                    $contactIds = array_merge(
                        $this->$method($d->id), $contactIds
                    );
                }
                $contactIds = array_unique($contactIds);
            }
        } else {
            return [0];
        }
        
        return empty($contactIds) ? [0] : $contactIds;
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有用户id
     *
     * @param $departmentId
     * @return array
     */
    function userIds($departmentId): array {
        
        $departmentIds[] = $departmentId;
        $department = new Department();
        $departmentIds = array_unique(
            array_merge(
                $department->subDepartmentIds($departmentId), $departmentIds
            )
        );
        $userIds = [];
        foreach ($departmentIds as $id) {
            $userIds = array_merge(
                DepartmentUser::whereDepartmentId($id)->pluck('user_id')->toArray(),
                $userIds
            );
        }
        
        return array_unique($userIds);
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有学生Id
     *
     * @param $departmentId
     * @return array
     */
    function studentIds($departmentId): array {
        
        return $this->getIds($departmentId, 'student');
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有监护人Id
     *
     * @param $departmentId
     * @return array
     */
    function custodianIds($departmentId): array {
        
        return $this->getIds($departmentId, 'custodian');
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有教职员工Id
     *
     * @param $departmentId
     * @return array
     */
    function educatorIds($departmentId): array {
        
        return $this->getIds($departmentId, 'educator');
        
    }
    
    /**
     * 返回对指定用户可见的所有部门Id
     *
     * @param $userId
     * @return array
     */
    function departmentIds($userId) {
        
        $departmentIds = [];
        $user = User::find($userId);
        $departments = $user->group->name == '运营'
            ? Department::find(Constant::ROOT_DEPARTMENT_ID)
            : $user->departments;
        foreach ($departments as $d) {
            $departmentIds[] = $d->id;
            $departmentIds = array_merge(
                $d->subDepartmentIds($d->id),
                $departmentIds
            );
        }
        
        return array_unique($departmentIds);
        
    }
    
    
    /**
     * 获取指定部门的联系人Id
     *
     * @param $departmentId
     * @param $type
     * @return array
     */
    private function getIds($departmentId, $type): array {
        
        $ids = [];
        $userIds = $this->userIds($departmentId);
        foreach ($userIds as $id) {
            $$type = User::find($id)->{$type};
            if ($$type) {
                $ids[] = $$type->id;
            }
        }
        
        return $ids;
        
    }
    

}

