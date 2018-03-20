<?php
namespace App\Helpers;

use App\Models\Action;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Menu;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use App\Policies\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
     * 返回对指定用户可见的所有学校Id
     *
     * @return array
     */
    function schoolIds() {
        
        $user = Auth::user();
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return School::all()->pluck('id')->toArray();
            case '企业':
                $corp = Corp::whereDepartmentId($user->topDeptId())->first();
                return $corp->schools->pluck('id')->toArray();
            case '学校':
                return [School::whereDepartmentId($user->topDeptId())->first()->id];
            default:
                return [$user->educator->school_id];
        }
        
    }
    
    /**
     * 返回对当前用户可见的所有年级Id
     *
     * @return array
     */
    function gradeIds() {
        
        $user = Auth::user();
        $role = $user->group->name;
        if (in_array($role, Constant::SUPER_ROLES)) {
            $schoolId = $this->schoolId();
            $gradeIds = School::find($schoolId)->grades->pluck('id')->toArray();
        } else {
            $departmentIds = $this->departmentIds($user->id);
            $gradeIds = [];
            foreach ($departmentIds as $id) {
                $department = Department::find($id);
                if ($department->departmentType->name == '年级') {
                    $gradeIds[] = $department->grade->id;
                }
            }
        }
        
        return empty($gradeIds) ? [0] : $gradeIds;
        
    }
    
    /**
     * 获取对当前用户可见的所有班级Id
     *
     * @return array
     */
    function classIds() {
        
        $user = Auth::user();
        $role = $user->group->name;
        if (in_array($role, Constant::SUPER_ROLES)) {
            $schoolId = $this->schoolId();
            $grades = School::find($schoolId)->grades;
            $classIds = [];
            foreach ($grades as $grade) {
                $classes = $grade->classes;
                foreach ($classes as $class) {
                    $classIds[] = $class->id;
                }
            }
        } else {
            $departmentIds = $this->departmentIds($user->id);
            $classIds = [];
            
            foreach ($departmentIds as $id) {
                $department = Department::find($id);
                if ($department->departmentType->name == '班级') {
                    $classIds[] = $department->squad->id;
                }
            }
        }
        
        return empty($classIds) ? [0] : $classIds;
        
    }
    
    /**
     * 获取对当前用户可见的、指定学校的联系人id
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
                    School::find($this->schoolId())->department_id
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
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            $department = School::find($this->schoolId())->department;
            $departmentIds[] = $department->id;
            return array_unique(
                array_merge($departmentIds, $department->subDepartmentIds($department->id))
            );
        }
        $departments = $user->departments;
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
     * 返回指定用户可管理的所有菜单id（校级以下角色没有管理菜单的权限）
     *
     * @return array
     */
    function menuIds() {
        
        $user = Auth::user();
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return Menu::all()->pluck('id')->toArray();
            case '企业':
                $corp = Corp::whereDepartmentId($user->topDeptId())->first();
                $menu = new Menu();
                $menuIds = $menu->subMenuIds($corp->menu_id);
                unset($menu);
                return $menuIds;
            case '学校':
                $school = School::whereDepartmentId($user->topDeptId())->first();
                $menu = new Menu();
                $menuIds = $menu->subMenuIds($school->menu_id);
                unset($menu);
                return $menuIds;
            default:
                return [];
                
        }
        
    }
    
    /**
     * 导出Excel文件
     *
     * @param array $records
     * @param string $fileName
     * @param string $sheetTitle
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function excel(array $records, $fileName = 'export', $sheetTitle = '导出数据') {
        
        $user = Auth::user();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator($user->realname)
            ->setLastModifiedBy($user->realname)
            ->setTitle('家校通')
            ->setSubject('家校通导出文件')
            ->setDescription('-')
            ->setKeywords('导出')
            ->setCategory('export');
        $spreadsheet->setActiveSheetIndex(0)->setTitle($sheetTitle);
        $spreadsheet->getActiveSheet()->fromArray($records, null, 'A1');
    
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . '"'. $fileName . '.xlsx"');
        header('Cache-Control: max-age=0');
    
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
    
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
    
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        return $writer->save('php://output');
        
    }
    
    /**
     * 返回上传文件路径
     *
     * @param $filename
     * @return string
     */
    public function uploadedFile($filename): string {
        
        return 'public/uploads/'
            . date('Y') . '/' . date('m') . '/' . date('d') . '/'
            . $filename;
        
    }
    
    /**
     * 返回指定单选列表对应的html
     *
     * @param array $items
     * @param $id
     * @return string
     */
    public function singleSelectList(array $items, $id) {
    
        $html = '<select class="form-control col-sm-6" id="%s" name="%s">';
        foreach ($items as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $html .= '</select>';
        
        return sprintf($html, $id, $id);
        
    }
    
    /**
     * 检查上传文件格式
     *
     * @param array $titles
     * @param array $format
     * @return bool
     */
    private function checkFileFormat(array $titles, array $format) {
        
        return empty(array_diff($titles, $format));
        
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

