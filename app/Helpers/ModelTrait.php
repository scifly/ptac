<?php
namespace App\Helpers;

use App\Models\Corp;
use App\Models\Menu;
use App\Models\User;
use Carbon\Carbon;
use ReflectionClass;
use App\Models\Squad;
use App\Models\Action;
use App\Models\School;
use App\Policies\Route;
use ReflectionException;
use App\Models\Department;
use App\Models\DepartmentUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Throwable;

/**
 * Trait ModelTrait
 * @package App\Helpers
 */
trait ModelTrait {
    
    /**
     * 批量启用/禁用记录
     *
     * @param Model $model
     * @return bool
     */
    function batch(Model $model) {
        
        $ids = array_values(Request::input('ids'));
        $action = Request::input('action');
        
        return $model->whereIn('id', $ids)->update([
            'enabled' => $action == 'enable' ? Constant::ENABLED : Constant::DISABLED
        ]);
        
    }
    
    /**
     * 批量启用/禁用联系人
     *
     * @param Model $model
     * @return bool
     * @throws \Throwable
     */
    function batchUpdateContact(Model $model) {
    
        $this->batch($model);
        $ids = Request::input('ids');
        $userIds = $model->whereIn('id', array_values($ids))->pluck('user_id')->toArray();
        Request::replace(['ids' => $userIds]);
    
        return (new User)->modify(Request::all());
        
    }
    
    /**
     * (批量)删除记录
     *
     * @param Model $model
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function del(Model $model, $id) {
        
        if (!$id) {
            $ids = Request::input('ids');
            DB::transaction(function () use ($ids, $model) {
                foreach ($ids as $id) { $model->{'purge'}($id); }
            });
    
            return true;
        }
    
        return $model->{'purge'}($id);
        
    }
    
    /**
     * 删除关联表中的所有数据
     *
     * @param $key
     * @param $class
     * @param $value
     * @return mixed
     */
    function delRelated($key, $class, $value) {
    
        /** @var Model $model */
        $class = '\\App\\Models\\' . $class;
        $model = new $class;
        $ids = $model->where($key, $value)->pluck('id')->toArray();
        Request::merge(['ids' => $ids]);
    
        return $model->{'remove'}();
        
    }
    
    /**
     * 判断指定记录能否被删除
     *
     * @param Model $model
     * @return bool
     * @throws ReflectionException
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
            if ($model->{$relation}) {
                if (get_class($model->{$relation}) == 'Illuminate\Database\Eloquent\Collection') {
                    if (count($model->{$relation})) { return false; }
                } else {
                    return false;
                }
            }
        }
        
        return true;
        
    }
    
    /**
     * 返回
     *
     * @param $d
     * @param $row
     * @param bool $contact - 是否为联系人（学生、教职员工、监护人）
     * @return string
     */
    function syncStatus($d, $row, $contact = true) {
        
        $user = Auth::user();
        $id = $row['id'];
        $status = Snippet::status($d);
        $status .= ($row['synced']
            ? sprintf(Snippet::ICON, 'fa-wechat text-green', '已同步')
            : sprintf(Snippet::ICON, 'fa-wechat text-gray', '未同步')
        );
        if ($contact) {
            $status .= ($row['subscribed']
                ? sprintf(Snippet::ICON, 'fa-registered text-green', '已关注')
                : sprintf(Snippet::ICON, 'fa-registered text-gray', '未关注')
            );
        }
        $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $id);
        $delLink = sprintf(Snippet::DT_LINK_DEL, $id);
    
        return
            $status .
            ($user->can('act', $this->uris()['edit']) ? $editLink : '') .
            ($user->can('act', $this->uris()['destroy']) ? $delLink : '');
        
    }
    
    /**
     * 获取当前控制器包含的方法所对应的路由对象数组
     *
     * @return array
     */
    function uris() {
        
        if (!Request::route()) { return null; }
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
        
        $schoolMenuId = (new Menu)->menuId(session('menuId'));
    
        return $schoolMenuId ? School::whereMenuId($schoolMenuId)->first()->id : null;
        
    }
    
    /**
     * 返回对指定用户可见的所有学校Id
     *
     * @param null $userId
     * @param null $corpId - 返回对指定用户可见的、指定企业的所有学校id
     * @return array
     */
    function schoolIds($userId = null, $corpId = null) {
        
        $user = !$userId ? Auth::user() : User::find($userId);
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return $corpId
                    ? School::whereCorpId($corpId)->pluck('id')->toArray()
                    : School::all()->pluck('id')->toArray();
            case '企业':
                $departmentId = head($user->departments->pluck('id')->toArray());
                $corp = Corp::whereDepartmentId($departmentId)->first();
                return $corp->schools->pluck('id')->toArray();
            case '学校':
                $departmentId = head($user->departments->pluck('id')->toArray());
                return [School::whereDepartmentId($departmentId)->first()->id];
            case '监护人':
                $classes = Squad::whereIn('department_id', $user->departments->pluck('id')->toArray())->get();
                $schoolIds = [];
                if (!isset($corpId)) { return $schoolIds; }
                foreach ($classes as $class) {
                    if ($class->grade->school->corp_id == $corpId) {
                        $schoolIds[] = $class->grade->school_id;
                    }
                }
                return array_unique($schoolIds);
            case '学生':
                return [$user->student->squad->grade->school_id];
            default:
                return [$user->educator->school_id];
        }
        
    }
    
    /**
     * 返回对当前用户可见的所有年级Id
     *
     * @param null $schoolId
     * @param null $userId
     * @return array
     */
    function gradeIds($schoolId = null, $userId = null) {
        
        $user = !$userId ? Auth::user() : User::find($userId);
        $schoolId = $schoolId ?? $this->schoolId();
        $role = $user->group->name;
        if (in_array($role, Constant::SUPER_ROLES)) {
            $gradeIds = School::find($schoolId)
                ->grades->pluck('id')->toArray();
        } else {
            $departmentIds = $this->departmentIds($user->id, $schoolId);
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
     * @param null $schoolId
     * @param null $userId
     * @return array
     */
    function classIds($schoolId = null, $userId = null) {
    
        $user = !$userId ? Auth::user() : User::find($userId);
        $schoolId = $schoolId ?? $this->schoolId();
        $role = $user->group->name;
        if (in_array($role, Constant::SUPER_ROLES)) {
            $grades = School::find($schoolId)->grades;
            $classIds = [];
            foreach ($grades as $grade) {
                $classes = $grade->classes;
                foreach ($classes as $class) {
                    $classIds[] = $class->id;
                }
            }
        } else {
            $departmentIds = $this->departmentIds($user->id, $schoolId);
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
     * 返回对当前用户可见的所有考试id
     *
     * @param null $schoolId
     * @param null $userId
     * @return array
     */
    function examIds($schoolId = null, $userId = null) {
    
        $user = !$userId ? Auth::user() : User::find($userId);
        $schoolId = $schoolId ?? $this->schoolId();
        $role = $user->group->name;
        if (in_array($role, Constant::SUPER_ROLES)) {
            $examIds = School::find($schoolId)->exams->pluck('id')->toArray();
        } else {
            $classIds = $this->classIds($schoolId);
            $exams = School::find($schoolId)->exams->pluck('class_ids', 'id');
            $examIds = [];
            foreach ($exams as $key => $value) {
                if (!empty(array_intersect($classIds, explode(',', $value)))) {
                    $examIds[] = $key;
                }
            }
        }
        
        return $examIds;
        
    }
    
    /**
     * 获取对当前用户可见的、指定学校的联系人id
     *
     * @param string $type - 联系人类型: custodian, student, educator
     * @param User|null $user
     * @param null $schoolId
     * @return array
     */
    function contactIds($type, User $user = null, $schoolId = null) {
        
        $user = $user ?? Auth::user();
        $schoolId = $schoolId ?? ($this->schoolId() ?? session('schoolId'));
        $role = $user->group->name;
        $method = $type . 'Ids';
        if (method_exists($this, $method)) {
            if (in_array($role, Constant::SUPER_ROLES)) {
                $contactIds = $this->$method(
                    School::find($schoolId)->department_id
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
     * 返回指定用户可访问的所有部门Id
     *
     * @param $userId
     * @param null $schoolId
     * @return array
     */
    function departmentIds($userId, $schoolId = null) {
        
        $departmentIds = [];
        $user = User::find($userId);
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            $schoolId = $schoolId ?? $this->schoolId();
            $department = $schoolId
                ? School::find($schoolId)->department
                : Department::find($user->group->name == '运营' ? 1 : $this->head($user));
            $departmentIds[] = $department->id;
            
            return array_unique(
                array_merge(
                    $departmentIds,
                    $department->subDepartmentIds($department->id)
                )
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
     * @param Menu $menu
     * @param null $userId
     * @return array
     */
    function menuIds(Menu $menu, $userId = null) {
    
        $user = !$userId ? Auth::user() : User::find($userId);
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return $menu::all()->pluck('id')->toArray();
            case '企业':
                $departmentId = head($user->departments->pluck('id')->toArray());
                $corp = Corp::whereDepartmentId($departmentId)->first();
                $menuIds = $menu->subMenuIds($corp->menu_id);
                return $menuIds;
            case '学校':
                $departmentId = head($user->departments->pluck('id')->toArray());
                $school = School::whereDepartmentId($departmentId)->first();
                $menuIds = $menu->subMenuIds($school->menu_id);
                return $menuIds;
            default:
                return [];
        }
        
    }
    
    /**
     * 返回指定用户所属的第一个部门id
     *
     * @param User $user
     * @return mixed
     */
    function head(User $user) {
        
        return head($user->departments->pluck('id')->toArray());
        
    }
    
    /**
     * 导出Excel文件
     *
     * @param array $records
     * @param string $fileName
     * @param string $sheetTitle
     * @param bool $download
     * @return bool
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function excel(array $records, $fileName = 'export', $sheetTitle = '导出数据', $download = true) {
        
        $user = Auth::user();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator($user ? $user->realname : 'ptac')
            ->setLastModifiedBy($user ? $user->realname : 'ptac')
            ->setTitle('智校+')
            ->setSubject('智校+')
            ->setDescription('-')
            ->setKeywords('导入导出')
            ->setCategory($fileName);
        $spreadsheet->setActiveSheetIndex(0)->setTitle($sheetTitle);
        $spreadsheet->getActiveSheet()->fromArray($records, null, 'A1');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        if ($download) {
            // Redirect output to a client’s web browser (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . '"' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            return $writer->save('php://output');
        }
        $dir = public_path() . '/uploads/' . date('Y/m/d/');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        return $writer->save($dir . '/' . $fileName . '.xlsx');
        
    }
    
    /**
     * 返回上传文件路径
     *
     * @param $filename
     * @return string
     */
    function uploadedFilePath($filename): string {
        
        return 'uploads/' . date('Y/m/d/') . $filename;
        
    }
    
    /**
     * 返回指定单选列表对应的html
     *
     * @param array $items
     * @param $id
     * @return string
     */
    function singleSelectList(array $items, $id) {
        
        $html = '<select class="form-control select2" id="%s" name="%s" style="width: %s"' .
            (sizeof($items) <=1 ? ' disabled ' : '') . '>';
        foreach ($items as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $html .= '</select>';
        
        return sprintf($html, $id, $id, '100%;');
        
    }
    
    /**
     * 获取当前请求对应的企业号id和“通讯录同步”Secret
     *
     * @return array
     */
    function tokenParams() {
        
        if (!session('corpId')) {
            $menu = new Menu();
            $corpMenuId = $menu->menuId(session('menuId'), '企业');
            abort_if(
                !$corpMenuId,
                HttpStatusCode::BAD_REQUEST,
                __('messages.bad_request')
            );
            $corp = Corp::whereMenuId($corpMenuId)->first();
        } else {
            $corp = Corp::find(session('corpId'));
        }
        
        return [
            $corp->corpid,
            $corp->contact_sync_secret
        ];
        
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
    
    /**
     * 返回Carbon格式日期
     *
     * @param $date
     * @return string
     */
    function humanDate($date) {
    
        Carbon::setLocale('zh');
        
        return isset($date)
            ? Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans()
            : '(n/a)';
        
    }
    
}

