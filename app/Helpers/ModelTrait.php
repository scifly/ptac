<?php
namespace App\Helpers;

use App\Models\{Action,
    App,
    CommType,
    Corp,
    Department,
    DepartmentUser,
    Exam,
    Grade,
    Group,
    MediaType,
    Menu,
    MessageType,
    School,
    Squad,
    Student,
    Tab,
    User};
use App\Policies\Route;
use Carbon\Carbon;
use Illuminate\Database\{Eloquent\Collection, Eloquent\Model, Query\Builder};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\{Auth, DB, Request, Storage};
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\{Exception, IOFactory, Spreadsheet, Writer\Exception as WriterException};
use ReflectionClass;
use ReflectionException;
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
        
        return $model->{'whereIn'}('id', array_values(Request::input('ids')))->update([
            'enabled' => Request::input('action') == 'enable' ? Constant::ENABLED : Constant::DISABLED,
        ]);
        
    }
    
    /**
     * 删除指定对象对应的记录
     *
     * @param array $classes
     * @param string $action
     * @param $field
     * @param $value
     * @return bool
     * @throws Throwable
     */
    function purge(array $classes, $field, $action = 'purge', $value = null) {
        
        try {
            DB::transaction(function () use ($classes, $action, $field, $value) {
                $fields = is_array($field) ? $field
                    : array_fill(0, sizeof($classes), $field);
                $values = $value
                    ? (is_array($value) ? $value : [$value])
                    : array_values(Request::input('ids'));
                $action != 'purge' ?: $fields[0] = 'id';
                array_map(
                    function ($class, $field) use ($action, $values) {
                        $model = $this->model($class);
                        switch ($action) {
                            case 'purge':
                            case 'reset':
                                /** @var Builder $builder */
                                $builder = $model->whereIn($field, $values);
                                $action == 'purge' ? $builder->delete() : $builder->update([$field => '0']);
                                break;
                            case 'clear':
                                $records = $model->all()->filter(
                                    function (Model $record) use ($values, $field) {
                                        return !empty(
                                        array_intersect(
                                            explode(',', $record->{$field}), $values
                                        )
                                        );
                                    }
                                );
                                /** @var Model $record */
                                foreach ($records as $record) {
                                    $val = implode(',', array_diff(
                                        explode(',', $record->{$field}), $values
                                    ));
                                    $record->update([$field => $val]);
                                }
                                break;
                            default:
                                break;
                        }
                    }, $classes, $fields
                );
            });
        } catch (\Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回指定名称对应的Model对象
     *
     * @param $class
     * @return object
     * @throws ReflectionException
     */
    function model($class) {
        
        return (new ReflectionClass("App\Models\\$class"))->newInstance();
        
    }
    
    /**
     * 保存绑定关系
     *
     * @param string $class
     * @param $value
     * @param array $ids
     * @param bool $forward
     * @return bool
     * @throws Throwable
     */
    function retain(string $class, $value, array $ids, bool $forward = true) {
        
        DB::transaction(function () use ($class, $value, $ids, $forward) {
            $model = $this->model($class);
            $fields = array_merge($model->getFillable(), ['created_at', 'updated_at']);
            $field = $fields[$forward ? 0 : 1];
            $model->where($field, $value)->delete();
            foreach ($ids as $id) {
                $records[] = array_combine($fields, [
                    $forward ? $value : $id,
                    $forward ? $id : $value,
                    Constant::ENABLED,
                    now()->toDateTimeString(),
                    now()->toDateTimeString(),
                ]);
            }
            $model->insert($records ?? []);
        });
        
        return true;
        
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
            if (!$method->isUserDefined() || !$method->isPublic() || $method->class != $class) continue;
            $doc = $method->getDocComment();
            if ($doc && stripos($doc, 'Has') !== false) $relations[] = $method->getName();
        }
        foreach ($relations as $relation) {
            if (!$model->{$relation}) continue;
            $isCollection = get_class($model->{$relation}) == 'Illuminate\Database\Eloquent\Collection';
            if (!$isCollection || ($isCollection && $model->{$relation}->count())) return false;
        }
        
        return true;
        
    }
    
    /**
     * 返回同步状态图标html
     *
     * @param boolean $value
     * @return string
     */
    function synced($value) {
        
        $color = $value ? 'text-green' : 'text-gray';
        $title = $value ? '已同步' : '未同步';
        
        return sprintf(Snippet::ICON, 'fa-weixin ' . $color, $title);
        
    }
    
    /**
     * 返回关注状态图标html
     *
     * @param boolean $value
     * @return string
     */
    function subscribed($value) {
        
        $color = $value ? 'text-green' : 'text-gray';
        $title = $value ? '已关注' : '未关注';
        
        return sprintf(Snippet::ICON, 'fa-registered ' . $color, $title);
        
    }
    
    /**
     * 获取当前控制器包含的方法所对应的路由对象数组
     *
     * @return array
     */
    function uris() {
        
        if (!Request::route()) return null;
        $controller = class_basename(Request::route()->controller);
        $routes = [];
        if ($tab = Tab::whereName($controller)->first()) {
            $routes = Action::where([
                ['tab_id', '=', $tab->id],
                ['route', '<>', null],
            ])->pluck('route', 'method')->toArray();
        }
        foreach ($routes as $key => $value) {
            $uris[$key] = new Route($value);
        }
        
        return $uris ?? [];
        
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
        
        $user = User::find($userId ?? Auth::id());
        $schools = new Collection;
        switch ($user->role($user->id)) {
            case '运营':
                $schools = School::all();
                break;
            case '企业':
                $corp = Corp::whereDepartmentId($this->topDeptId($user))->first();
                $schools = $corp->schools;
                break;
            case '学生':
                $schools->push($user->student->squad->grade->school);
                break;
            default:
                if ($user->custodian) {
                    foreach ($user->custodian->students as $student) {
                        $schoolIds[] = $student->squad->grade->school_id;
                    }
                }
                !$user->educator ?: $schoolIds[] = $user->educator->school_id;
                $schools = School::whereIn('id', array_unique($schoolIds ?? []))->get();
                break;
        }
        
        return $schools->when(
            $corpId, function (Collection $schools) use ($corpId) {
            return $schools->where('corp_id', $corpId);
        })->pluck('id')->toArray();
        
    }
    
    /**
     * 返回对当前用户可见的所有年级Id
     *
     * @param null $schoolId
     * @param null $userId
     * @return array
     */
    function gradeIds($schoolId = null, $userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $schoolId = $schoolId ?? $this->schoolId();
        $grades = in_array($user->role($user->id), Constant::SUPER_ROLES)
            ? School::find($schoolId)->grades
            : Grade::whereIn('department_id', $this->departmentIds($user->id, $schoolId))->get();
        
        return $grades->isEmpty() ? [0] : $grades->pluck('id')->toArray();
        
    }
    
    /**
     * 获取对当前用户可见的所有班级Id
     *
     * @param null $schoolId
     * @param null $userId
     * @return array
     */
    function classIds($schoolId = null, $userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $schoolId = $schoolId ?? $this->schoolId();
        $classes = in_array($user->role($user->id), Constant::SUPER_ROLES)
            ? School::find($schoolId)->classes
            : Squad::whereIn('department_id', $this->departmentIds($user->id, $schoolId))->get();
        
        return $classes->isEmpty() ? [0] : $classes->pluck('id')->toArray();
        
    }
    
    /**
     * 返回对当前用户可见的所有考试id
     *
     * @param null $schoolId
     * @param null $userId
     * @return array
     */
    function examIds($schoolId = null, $userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $schoolId = $schoolId ?? $this->schoolId();
        if (in_array($user->role($user->id), Constant::SUPER_ROLES)) {
            $examIds = School::find($schoolId)->exams->pluck('id')->toArray();
        } else {
            $classIds = $this->classIds($schoolId);
            $examIds = School::find($schoolId)->exams->filter(
                function (Exam $exam) use ($classIds) {
                    $class_ids = explode(',', $exam->class_ids);
                    
                    return !empty(array_intersect($classIds, $class_ids));
                }
            )->pluck('id')->toArray();
        }
        
        return $examIds;
        
    }
    
    /**
     * 获取对当前用户可见的、指定学校的联系人id
     *
     * @param string $type - 联系人类型: custodian, student, educator
     * @param User|null $user
     * @param null $schoolId
     * @return array|null
     * @throws ReflectionException
     */
    function contactIds($type, User $user = null, $schoolId = null) {
        
        $user = $user ?? Auth::user();
        $schoolId = $schoolId ?? ($this->schoolId() ?? session('schoolId'));
        $userIds = [];
        if (in_array($user->role($user->id), Constant::SUPER_ROLES)) {
            $userIds = $this->userIds(
                School::find($schoolId)->department_id, $type
            );
        } else {
            foreach ($user->deptIds() as $id) {
                $userIds = array_merge(
                    $this->userIds($id, $type), $userIds
                );
            }
        }
        $userIds = array_unique($userIds);
        
        return (empty($userIds)) ? [0]
            : User::with($type)->whereIn('id', $userIds)
                ->get()->pluck($type . '.id')->toArray();
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有用户id
     *
     * @param $departmentId
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    function userIds($departmentId, $type = null): array {
        
        $departmentIds = array_merge(
            [$departmentId],
            (new Department)->subIds($departmentId)
        );
        $userIds = DepartmentUser::whereIn('department_id', $departmentIds)
            ->pluck('user_id')->toArray();
        if (!$type) return array_unique($userIds);
        $contact = (new ReflectionClass('App\\Models\\' . ucfirst($type)))->newInstance();
        
        return array_unique(
            $contact->whereIn('user_id', $userIds)->pluck('user_id')->toArray()
        );
        
    }
    
    /**
     * 返回指定用户可访问的所有部门Id
     *
     * @param $userId
     * @param null $schoolId
     * @return array
     */
    function departmentIds($userId = null, $schoolId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $role = $user->role($userId ?? Auth::id());
        if (in_array($role, Constant::SUPER_ROLES)) {
            $schoolId = $schoolId ?? $this->schoolId();
            $department = $schoolId
                ? School::find($schoolId)->department
                : Department::find($role == '运营' ? 1 : $this->topDeptId($user));
            $departmentIds[] = $department->id;
            
            return array_unique(
                array_merge(
                    $departmentIds,
                    $department->subIds($department->id)
                )
            );
        }
        $d = new Department;
        $deptIds = $user->deptIds();
        foreach ($deptIds as $id) {
            $departmentIds[] = $id;
            $departmentIds = array_merge(
                $d->subIds($id),
                $departmentIds
            );
        }
        
        return array_unique($departmentIds ?? []);
        
    }
    
    /**
     * 返回指定用户可管理的所有菜单id（校级以下角色没有管理菜单的权限）
     *
     * @param null $userId
     * @return array
     * @throws ReflectionException
     */
    function menuIds($userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $role = $user->role($user->id);
        if ($role == '运营') {
            $menuIds = Menu::all()->pluck('id')->toArray();
        } elseif (in_array($role, ['企业', '学校'])) {
            $className = 'App\\Models\\' . ($role == '企业' ? 'Corp' : 'School');
            $model = (new ReflectionClass($className))->newInstance();
            $menuIds = (new Menu)->subIds(
                $model::whereDepartmentId($this->topDeptId($user))->first()->menu_id
            );
        }
        
        return $menuIds ?? [];
        
    }
    
    /**
     * 从上传的excel文件中获取需要导入的数据
     *
     * @param bool $removeTitles
     * @return array
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    function uploader($removeTitles = true) {
        
        $code = HttpStatusCode::INTERNAL_SERVER_ERROR;
        abort_if(
            Request::method() != 'POST', $code,
            __('messages.file_upload_failed')
        );
        $file = Request::file('file');
        abort_if(
            empty($file) || !$file->isValid(), $code,
            __('messages.empty_file')
        );
        $ext = $file->getClientOriginalExtension();
        $realPath = $file->getRealPath();
        $filename = date('His') . uniqid() . '.' . $ext;
        $stored = Storage::disk('uploads')->put(
            date('Y/m/d/', time()) . $filename,
            file_get_contents($realPath)
        );
        abort_if(
            !$stored, $code,
            __('messages.file_upload_failed')
        );
        $spreadsheet = IOFactory::load(
            $this->filePath($filename)
        );
        $records = $spreadsheet->getActiveSheet()->toArray(
            null, true, true, true
        );
        $records = array_filter(
            array_values($records), 'implode'
        );
        abort_if(
            !empty(array_diff(self::EXCEL_TITLES, array_values($records[0]))),
            HttpStatusCode::NOT_ACCEPTABLE, __('messages.invalid_file_format')
        );
        Storage::disk('uploads')->delete($filename);
        if ($removeTitles) array_shift($records);
        
        return $records;
        
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
     * @throws WriterException
     */
    function excel(array $records, $fileName = 'export', $sheetTitle = '导出数据', $download = true) {
        
        $user = Auth::user();
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()->setCreator($user ? $user->realname : 'ptac')
            ->setLastModifiedBy($user ? $user->realname : 'ptac')
            ->setTitle(config('app.name'))
            ->setSubject(config('app.name'))
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
        $dir = public_path() . '/' . $this->filePath();
        file_exists($dir) ?: mkdir($dir, 0777, true);
        
        return $writer->save($dir . '/' . $fileName . '.xlsx');
        
    }
    
    /**
     * 返回上传/下载文件路径
     *
     * @param $filename
     * @return string
     */
    function filePath($filename = ''): string {
        
        return 'uploads/' . date('Y/m/d/') . $filename;
        
    }
    
    /**
     * 返回消息Datatable过滤器（通信方式、应用及消息类型）下拉列表html
     *
     * @return array
     */
    function messageFilters() {
        
        $optionAll = [null => '全部'];
        $htmlCommType = $this->singleSelectList(
            $optionAll + CommType::all()->pluck('name', 'id')->toArray(),
            'filter_commtype'
        );
        $htmlMediaType = $this->singleSelectList(
            $optionAll + MediaType::all()->pluck('remark', 'id')->toArray(),
            'filter_mediatype'
        );
        $htmlMessageType = $this->singleSelectList(
            $optionAll + MessageType::whereEnabled(1)->pluck('name', 'id')->toArray(),
            'filter_message_type'
        );
        
        return [
            $optionAll,
            $htmlCommType,
            $htmlMediaType,
            $htmlMessageType,
        ];
        
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
            (sizeof($items) <= 1 ? ' disabled ' : '') . '>';
        foreach ($items as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $html .= '</select>';
        
        return sprintf($html, $id, $id, '100%;');
        
    }
    
    /**
     * 返回日期时间过滤字段对应的html
     *
     * @param $title
     * @param bool $timepicker
     * @return string
     */
    function inputDateTimeRange($title, $timepicker = true) {
        
        $class = 'form-control ' . ($timepicker ? 'dtrange' : 'drange');
        
        return '<input type="text" class="' . $class . '" title="按' . $title . '过滤" />';
        
    }
    
    /**
     * 返回对当前登录用户可见的所有用户id
     *
     * @return string
     */
    function visibleUserIds() {
        
        if (in_array(Auth::user()->role(), Constant::SUPER_ROLES)) {
            $school = School::find($this->schoolId());
            $departmentId = $school->department_id;
            $departmentIds = array_merge(
                [$departmentId], $school->department->subIds($departmentId)
            );
        } else {
            $departmentIds = $this->departmentIds();
        }
        $userIds = implode(',', array_unique(
                DepartmentUser::whereIn('department_id', array_unique($departmentIds))
                    ->pluck('user_id')->toArray()
            )
        );
        
        return !empty($userIds) ? $userIds : '0';
        
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
    
    /**
     * 返回Http请求对应的方法名称
     *
     * @return mixed
     */
    function method() {
        
        return explode('/', Request::path())[1];
        
    }
    
    /**
     * 返回批量更新请求验证规则
     *
     * @param $rules
     */
    function batchRules(&$rules) {
        
        if ($this->method() == 'update' && !Request::route('id')) {
            $rules = [
                'ids'    => 'required|array',
                'action' => [
                    'required', Rule::in(Constant::BATCH_OPERATIONS),
                ],
                'field'  => 'required|string',
            ];
        }
        
    }
    
    /**
     * 返回指定角色的表单数据过滤结果
     *
     * @param FormRequest $request
     * @param null $role
     * @return array
     * @throws ReflectionException
     */
    function contactInput(FormRequest $request, $role) {
        
        $input = $request->all();
        $userid = uniqid('ptac_');
        switch ($role) {
            case 'student':
            case 'custodian':
                Request::route('id') ?: $input['user'] += [
                    'username' => $userid,
                    'password' => '12345678',
                ];
                $position = ($role == 'student' ? '学生' : '监护人');
                $groupId = Group::whereName($position)->first()->id;
                if (isset($input['singular']) && !$input['singular']) {
                    $group = isset($input['user_id'])
                        ? User::find($input['user_id'])->group
                        : Group::where(['school_id' => $this->schoolId(), 'name' => '教职员工'])->first();
                    $position = $group->name . '/监护人';
                    $groupId = $group->id;
                }
                $input['user']['ent_attrs']['position'] = $position;
                $input['user']['group_id'] = $groupId;
                $input['enabled'] = $input['user']['enabled'];
                if (!empty($input['student_ids'])) {
                    foreach ($input['student_ids'] as $key => $studentId) {
                        $student = Student::find($studentId);
                        abort_if(
                            !$student || !in_array($studentId, $this->contactIds('student')),
                            HttpStatusCode::NOT_FOUND,
                            __('messages.student.not_found') . ':' . $studentId
                        );
                        $departmentIds[] = $student->squad->department_id;
                        $rses[$studentId] = $input['relationships'][$key];
                    }
                    $input['relationships'] = $rses ?? [];
                    $input['departmentIds'] = $departmentIds ?? [];
                }
                break;
            case 'educator':
            case 'operator':
                $role == 'educator' ?: $input['user'] = $input;
                $input['user']['ent_attrs']['position'] = Group::find($input['user']['group_id'])->name;
                if ($role == 'educator') {
                    $input['enabled'] = $input['user']['enabled'];
                    $input['school_id'] = $this->schoolId();
                    $input['selectedDepartments'] = $input['selectedDepartments'] ?? [];
                    if (
                        !array_key_exists(0, $input['cs']['class_ids']) &&
                        !array_key_exists(0, $input['cs']['subject_ids'])
                    ) {
                        foreach ($input['cs']['class_ids'] as $classId) {
                            if (!$classId) continue;
                            $class = Squad::find($classId);
                            abort_if(
                                !$class || !in_array($class->id, $this->classIds()),
                                HttpStatusCode::NOT_FOUND,
                                __('messages.class.not_found') . ':' . $classId
                            );
                            $classDeptIds[] = $class->department_id;
                        }
                        $input['selectedDepartments'] += $classDeptIds ?? [];
                    }
                } else {
                    if (Group::find($input['user']['group_id'])->name == '学校') {
                        $input += [
                            'school_id' => $input['school_id'],
                            'enabled'   => $input['user']['enabled'],
                        ];
                        isset($input['id']) ?: $input += ['singular' => 1];
                    }
                }
                break;
            default:
                break;
        }
        Request::route('id') ?: $input['user']['ent_attrs']['userid'] = $userid;
        $input['user']['ent_attrs'] = json_encode(
            $input['user']['ent_attrs'],
            JSON_UNESCAPED_UNICODE
        );
        
        return $input;
        
    }
    
    /**
     * 返回企业微信应用对象
     *
     * @param $corpId
     * @param null $name
     * @return App|Model|object|null
     */
    function app($corpId, $name = null) {
        
        $app = App::where([
            'corp_id' => $corpId,
            'name'    => $name ?? config('app.name'),
        ])->first();
        abort_if(
            !$app, HttpStatusCode::NOT_FOUND,
            __('messages.app.not_found')
        );
        
        return $app;
        
    }
    
    /**
     * 返回指定用户所属的最顶级部门id
     *
     * @param User $user
     * @return int|mixed
     */
    function topDeptId(User $user = null) {
        
        $user = $user ?? Auth::user();
        $departmentIds = $user->departments->pluck('id')->toArray();
        sort($departmentIds);
        $topDeptId = head($departmentIds);
        
        return $topDeptId ? $topDeptId : 1;
        
    }
    
}

