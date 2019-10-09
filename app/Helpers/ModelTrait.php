<?php
namespace App\Helpers;

use App\Models\{App,
    Corp,
    Department,
    DepartmentTag,
    DepartmentType,
    DepartmentUser,
    Exam,
    Grade,
    Group,
    GroupMenu,
    Menu,
    MenuTab,
    School,
    Squad,
    Student,
    User};
use Exception;
use Form;
use Html;
use Illuminate\Database\{Eloquent\Collection, Eloquent\Model, Query\Builder};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SCollection;
use Illuminate\Support\Facades\{Auth, DB, Request, Session, Storage};
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
     * @param Model $model
     * @param array $data
     * @param $id
     * @param $next
     * @return bool
     * @throws Throwable
     */
    function revise(Model $model, array $data, $id, $next) {
        
        try {
            DB::transaction(function () use ($model, $data, $id, $next) {
                if (!$id) {
                    $this->batch($model);
                } else {
                    throw_if(
                        !$record = $model->{'find'}($id),
                        new Exception(__('messages.not_found'))
                    );
                    $record->{'update'}($data);
                    !$next ?: $next($record);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除指定对象对应的记录
     *
     * @param $id
     * @param array|null $params
     * @return bool
     * @throws Throwable
     */
    function purge($id, array $params = null) {
        
        try {
            DB::transaction(function () use ($id, $params) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                if (!empty($ids)) {
                    $this->model(get_called_class())->whereIn('id', $ids)->delete();
                    foreach ($params ?? [] as $key => $classes) {
                        [$action, $field] = explode('.', $key);
                        foreach ($classes as $class) {
                            $model = $this->model($class);
                            if (in_array($action, ['purge', 'reset'])) {
                                /** @var Builder $builder */
                                $builder = $model->whereIn($field, $ids);
                                if ($action == 'purge') {
                                    Request::replace(['ids' => $builder->pluck('id')->toArray()]);
                                    $model->remove();
                                } else {
                                    $builder->update([$field => 0]);
                                }
                            } elseif ($action == 'clear') {
                                /** @var Model $record */
                                foreach ($model->all() as $record) {
                                    $original = collect(explode(',', $record->{$field}));
                                    if ($original->isEmpty()) continue;
                                    $record->update([$field => $original->diff($ids)]);
                                }
                            }
                        }
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除运营/企业/学校/(年级/班级)
     *
     * @param $id
     * @param $params
     * @return bool
     * @throws Throwable
     */
    function mdPurge($id, array $params) {
    
        try {
            DB::transaction(function () use ($id, $params) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $deptIds = $menuIds = collect([]);
                foreach ($ids as $id) {
                    $record = $this->find($id);
                    if ($deptId = $record->department_id) {
                        $deptIds->prepend($deptId);
                        $deptIds = $deptIds->merge(
                            $record->dept->subIds($deptId)
                        );
                    }
                    if ($menuId = $record->menu_id) {
                        $menuIds->prepend($menuId);
                        $menuIds = $menuIds->merge(
                            $record->menu->subIds($menuId)
                        );
                    }
                }
                array_map(
                    function ($class) use ($deptIds, $menuIds) {
                        $m = 'Menu'; $d = 'Department';
                        $c = strpos($class, $m) !== false ? $m : $d;
                        $field = $class == $c ? 'id' : lcfirst($c) . '_id';
                        $ids = $c == $m ? $menuIds : $deptIds;
                        $class::{'whereIn'}($field, $ids)->delete();
                    }, [
                        'Department', 'Menu', 'DepartmentUser',
                        'DepartmentTag', 'MenuTab', 'GroupMenu'
                    ]
                );
                $this->purge($id, $params);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回指定名称对应的Model对象
     *
     * @param $name
     * @return object
     * @throws ReflectionException
     */
    function model($name) {
        
        $ns = 'App\Models';
        $class = strpos($name, $ns) !== false
            ? $name : join('\\', [$ns, ucfirst($name)]);
        
        return (new ReflectionClass($class))->newInstance();
        
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
        
        try {
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
        } catch (Exception $e) {
            throw $e;
        }
        
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
     * 根据当前菜单Id返回学校Id
     *
     * @return int|mixed
     */
    function schoolId() {
        
        $menuId = (new Menu)->menuId(session('menuId'));
        
        return $menuId ? School::whereMenuId($menuId)->first()->id : null;
        
    }
    
    /**
     * 根据角色 & 菜单id获取corp_id
     *
     * @param null $deptId
     * @return int|mixed
     */
    function corpId($deptId = null) {
        
        if ($deptId) {
            $dept = Department::find($deptId);
            while ($dept->dType->name != '企业') {
                $dept = $dept->parent;
                return $dept ? $this->corpId($dept->id) : null;
            }
            $corp = $dept->corp;
            $corpId = $corp ? $corp->id : null;
        } else {
            if (!$menuId = Session::exists('menuId')) return null;
            $user = Auth::user();
            $menu = new Menu;
            $role = $user->role();
            if (in_array($role, ['运营', '企业'])) {
                $mId = $menu->menuId($menuId, '企业');
                $corpId = !$mId ?: Corp::whereMenuId($mId)->first()->id;
            } elseif ($role == '学校') {
                $mId = $menu->menuId($menuId);
                $corpId = School::whereMenuId($mId)->first()->corp_id;
            } else {
                $corpId = $user->educator->school->corp_id;
            }
        }
        
        return $corpId;
        
    }
    
    /**
     * 返回对指定用户可见的所有学校Id
     *
     * @param null $userId
     * @param null $corpId - 返回对指定用户可见的、指定企业的所有学校id
     * @return SCollection
     */
    function schoolIds($userId = null, $corpId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $schools = new Collection;
        $role = $user->role($user->id);
        if ($role == '运营') {
            $schools = School::all();
        } elseif ($role == '企业') {
            $schools = Corp::whereDepartmentId($user->depts->first()->id)->first()->schools;
        } elseif ($role == '学生') {
            $schools->push($user->student->squad->grade->school);
        } elseif ($custodian = $user->custodian) {
            foreach ($custodian->students as $student) {
                $schools->push($student->squad->grade->school);
            }
        } elseif ($educator = $user->educator) {
            $schools->push($educator->school);
        }
        
        return $schools->when(
            $corpId, function (Collection $schools) use ($corpId) {
            return $schools->where('corp_id', $corpId);
        })->unique()->pluck('id');
        
    }
    
    /**
     * 返回对当前用户可见的所有年级Id
     *
     * @param null $schoolId
     * @param null $userId
     * @return SCollection
     * @throws Exception
     */
    function gradeIds($schoolId = null, $userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $schoolId = $schoolId ?? $this->schoolId();
        $grades = in_array($user->role($user->id), Constant::SUPER_ROLES)
            ? School::find($schoolId)->grades
            : Grade::whereIn('department_id', $this->departmentIds($user->id));
        
        return $grades->isEmpty() ? collect([0]) : $grades->pluck('id');
        
    }
    
    /**
     * 获取对当前用户可见的所有班级Id
     *
     * @param null $schoolId
     * @param null $userId
     * @return SCollection
     * @throws Exception
     */
    function classIds($schoolId = null, $userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $schoolId = $schoolId ?? $this->schoolId();
        $classes = in_array($user->role($user->id), Constant::SUPER_ROLES)
            ? School::find($schoolId)->classes
            : Squad::whereIn('department_id', $this->departmentIds($user->id));
        
        return $classes->isEmpty() ? collect([0]) : $classes->pluck('id');
        
    }
    
    /**
     * 返回对当前用户可见的所有考试id
     *
     * @param null $schoolId
     * @param null $userId
     * @return SCollection
     * @throws Exception
     */
    function examIds($schoolId = null, $userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $schoolId = $schoolId ?? $this->schoolId();
        if (in_array($user->role($user->id), Constant::SUPER_ROLES)) {
            $exams = School::find($schoolId)->exams;
        } else {
            $classIds = $this->classIds($schoolId);
            $exams = School::find($schoolId)->exams->filter(
                function (Exam $exam) use ($classIds) {
                    return $classIds->intersect(explode(',', $exam->class_ids))->isNotEmpty();
                }
            );
        }
        
        return $exams->pluck('id');
        
    }
    
    /**
     * 获取对当前用户可见的、指定学校的联系人id
     *
     * @param string|null $type - 联系人类型: user(null), custodian, student, educator
     * @param User|null $user
     * @param null $schoolId
     * @return SCollection
     * @throws ReflectionException
     */
    function contactIds($type = null, User $user = null, $schoolId = null) {
        
        $user = $user ?? Auth::user();
        $schoolId = $schoolId ?? ($this->schoolId() ?? session('schoolId'));
        $userIds = collect([]);
        $d = new Department;
        if (in_array($user->role($user->id), Constant::SUPER_ROLES)) {
            $userIds = $d->userIds(
                School::find($schoolId)->department_id, $type
            );
        } else {
            foreach ($user->deptIds() as $id) {
                $userIds = $d->userIds($id, $type)->merge($userIds);
            }
        }
        $userIds = $userIds->unique();
        
        return $userIds->isEmpty() ? collect([0])
            : User::with($type)->whereIn('id', $userIds)->get()->pluck($type . '.id');
        
    }
    
    /**
     * 返回指定用户可访问的所有部门Id
     *
     * @param $userId
     * @return SCollection
     * @throws Exception
     */
    function departmentIds($userId = null) {
        
        try {
            $userId = $userId ?? Auth::id();
            $user = User::find($userId);
            $role = $user->role($userId);
            $d = new Department;
            $deptIds = collect([]);
            if (in_array($role, Constant::SUPER_ROLES)) {
                $dept = $this->schoolId()
                    ? School::find($this->schoolId())->department
                    : $user->depts->first();
                $deptIds = collect([$dept->id])->merge(
                    $d->subIds($dept->id)
                );
            } else {
                foreach ($user->deptIds() as $deptId) {
                    $deptIds[] = $deptId;
                    $deptIds = $d->subIds($deptId)->merge($deptIds);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
        
        return $deptIds;
        
    }
    
    function tagIds($userId = null) {
    
    }
    
    /**
     * 返回指定用户可管理的所有菜单id（校级以下角色没有管理菜单的权限）
     *
     * @param null $userId
     * @return SCollection
     * @throws ReflectionException
     */
    function menuIds($userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        $role = $user->role($user->id);
        if ($role == '运营') {
            $menuIds = Menu::all()->pluck('id');
        } elseif (in_array($role, ['企业', '学校'])) {
            $model = $this->model($role == '企业' ? 'Corp' : 'School');
            $menuIds = (new Menu)->subIds(
                $model::whereDepartmentId($user->depts->first()->id)->first()->menu_id
            );
        }
        
        return $menuIds ?? collect([]);
        
    }
    
    /**
     * 从上传的excel文件中获取需要导入的数据
     *
     * @param bool $removeTitles
     * @return array
     * @throws Throwable
     */
    function records($removeTitles = true) {
        
        $ex = array_map(
            function ($msg) { return new Exception(__('messages.' . $msg)); },
            ['file_upload_failed', 'empty_file', 'invalid_file_format']
        );
        try {
            throw_if(Request::method() != 'POST', $ex[0]);
            $file = Request::file('file');
            throw_if(empty($file) || !$file->isValid(), $ex[1]);
            $ext = $file->getClientOriginalExtension();
            $realPath = $file->getRealPath();
            $filename = date('His') . uniqid() . '.' . $ext;
            $stored = Storage::disk('uploads')->put(
                date('Y/m/d/', time()) . $filename,
                file_get_contents($realPath)
            );
            throw_if(!$stored, $ex[0]);
            $spreadsheet = IOFactory::load($this->filePath($filename));
            $records = $spreadsheet->getActiveSheet()->toArray(
                null, true, true, true
            );
            $records = array_filter(
                array_values($records), 'join'
            );
            throw_if(!empty(array_diff(self::EXCEL_TITLES, array_values($records[0]))), $ex[2]);
            Storage::disk('uploads')->delete($filename);
            !$removeTitles ?: array_shift($records);
        } catch (Exception $e) {
            throw $e;
        }
        
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
     */
    function excel(array $records, $fileName = 'export', $sheetTitle = '导出数据', $download = true) {
        
        try {
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
                $cmd = $writer->save('php://output');
            } else {
                $dir = public_path() . '/' . $this->filePath();
                file_exists($dir) ?: mkdir($dir, 0777, true);
                $cmd = $writer->save($dir . '/' . $fileName . '.xlsx');
            }
        } catch (Exception $e) {
            throw $e;
        }
        
        return $cmd;
        
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
     * 返回指定单选列表对应的html
     *
     * @param array|SCollection $items
     * @param $id
     * @return string
     */
    function htmlSelect($items, $id) {
        
        return Form::select($id, $items, null, [
            'id'       => $id,
            'class'    => 'form-control select2',
            'style'    => 'width: 100%;',
            'disabled' => sizeof($items) <= 1,
        ])->toHtml();
        
    }
    
    /**
     * 返回日期时间过滤字段对应的html
     *
     * @param $title
     * @param bool $timepicker
     * @return string
     */
    function htmlDTRange($title, $timepicker = true) {
        
        return Form::text(null, null, [
            'class' => 'form-control ' . ($timepicker ? 'dtrange' : 'drange'),
            'title' => '按' . $title . '过滤',
        ])->toHtml();
        
    }
    
    /**
     * 返回对当前登录用户可见的所有用户id
     *
     * @param null $schoolId
     * @return string
     * @throws Exception
     */
    function visibleUserIds($schoolId = null) {
        
        $school = School::find($schoolId ?? $this->schoolId());
        $userIds = DepartmentUser::whereIn('department_id', $this->departmentIds())->pluck('user_id');
        if (!in_array(Auth::user()->role(), Constant::SUPER_ROLES)) {
            $userIds = $userIds->union($school->educators->pluck('user_id'));
        }
        
        return $userIds->isNotEmpty()
            ? $userIds->unique()->join(',')
            : null;
        
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
                    'required', Rule::in(['enable', 'disable', 'delete']),
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
     * @throws Exception
     * @throws Throwable
     */
    function contactInput(FormRequest $request, $role) {
        
        try {
            $input = $request->all();
            $userid = uniqid('ptac_');
            if (in_array($role, ['student', 'custodian'])) {
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
                        throw_if(
                            !$student || !$this->contactIds('student')->flip()->has($studentId),
                            new Exception(__('messages.student.not_found') . ':' . $studentId)
                        );
                        $departmentIds[] = $student->squad->department_id;
                        $rses[$studentId] = $input['relationships'][$key];
                    }
                    $input['relationships'] = $rses ?? [];
                    $input['departmentIds'] = $departmentIds ?? [];
                }
            } else {
                $role == 'educator' ?: $input['user'] = $input;
                $position = Group::find($input['user']['group_id'])->name;
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
                            throw_if(
                                !$class || !$this->classIds()->flip()->has($class->id),
                                new Exception(__('messages.class.not_found') . ':' . $classId)
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
            }
            if (!Request::route('id')) {
                $input['user']['ent_attrs'] = json_encode(
                    ['userid' => $userid, 'position' => $position ?? ''],
                    JSON_UNESCAPED_UNICODE
                );
            } else {
                $input['user']['ent_attrs->position'] = $position ?? '';
            }
        } catch (Exception $e) {
            throw $e;
        }
        
        return $input;
        
    }
    
    /**
     * 返回企业微信应用对象
     *
     * @param $corpId
     * @param null $name
     * @return App|Model|object|null
     * @throws Throwable
     */
    function corpApp($corpId, $name = null) {
        
        try {
            $where = [
                'corp_id' => $corpId,
                'name'    => $name ?? config('app.name'),
            ];
            throw_if(
                !$app = App::where($where)->first(),
                new Exception(__('messages.app.not_found'))
            );
        } catch (Exception $e) {
            throw $e;
        }
        
        return $app;
        
    }
    
    /**
     * @param $class
     * @param $content
     * @return string
     */
    function badge($class, $content = null) {
        
        return $content ? Html::tag('span', $content, ['class' => $class])->toHtml() : '-';
        
    }
    
    /**
     * @param $status
     * @param string $enabled
     * @param string $disabled
     * @return string
     */
    function state($status, $enabled = '已启用', $disabled = '未启用') {
        
        if (!isset($status)) return '';
        if (!is_numeric($status)) return $status;
        $color = 'text-' . ($status ? 'green' : 'gray');
        $title = $status ? $enabled : $disabled;
        
        return Html::tag('i', '', [
            'class' => 'fa fa-circle ' . $color,
            'title' => $title,
            'style' => 'width: 20px; margin: 0 10px;',
        ])->toHtml();
        
    }
    
    /**
     * @param $d
     * @return string
     */
    function avatar($d) {
        
        return Html::image(!empty($d) ? $d : '/img/default.png', '', [
            'class' => 'img-circle', 'style' => 'height:16px;',
        ])->toHtml();
        
    }
    
    /**
     * @param $d
     * @return string
     */
    function gender($d) {
        
        return Html::tag('i', '', [
            'class' => 'fa fa-' . ($d ? 'mars' : 'venus'),
        ])->toHtml();
        
    }
    
    /**
     * @param $d
     * @param $type
     * @return string
     */
    function iconHtml($d, $type = null) {
        
        $dt = DepartmentType::whereRemark($type)->first();
        $color = $dt ? $dt->color : '';
        $class = $dt ? $dt->icon : $d;
        $icon = Html::tag('i', '', [
            'class' => $class . (!empty($color) ? ' ' . $color : ''),
            'style' => 'width: 20px; margin: 0 5px;',
        ])->toHtml();
        $text = $dt ? Html::tag('span', $d, ['class' => $color])->toHtml() : '';
        
        return join([$icon, $text]);
        
    }
    
    /**
     * @param $id
     * @param $title
     * @param $class
     * @return string
     */
    function anchor($id, $title, $class) {
        
        return Html::link(
            '#', Html::tag('i', '', ['class' => 'fa ' . $class]),
            ['id' => $id, 'title' => $title, 'style' => 'margin-left: 15px;'],
            null, false
        )->toHtml();
        
    }
    
}

