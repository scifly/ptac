<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait, Snippet};
use App\Jobs\ImportStudent;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\{Facades\Auth, Facades\DB, Facades\Request, Facades\Storage};
use PhpOffice\PhpSpreadsheet\IOFactory;
use ReflectionException;
use Throwable;

/**
 * App\Models\Student 学生
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $class_id 班级ID
 * @property string $student_number 学号
 * @property string $card_number 卡号
 * @property int $oncampus 是否住校
 * @property string $birthday 生日
 * @property string $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Custodian[] $custodians
 * @property-read Collection|ScoreTotal[] $scoreTotals
 * @property-read Collection|Score[] $scores
 * @property-read Squad $squad
 * @property-read User $user
 * @property-read Collection|Consumption[] $consumptions
 * @property-read Collection|Module[] $modules
 * @method static Builder|Student whereBirthday($value)
 * @method static Builder|Student whereCardNumber($value)
 * @method static Builder|Student whereClassId($value)
 * @method static Builder|Student whereCreatedAt($value)
 * @method static Builder|Student whereEnabled($value)
 * @method static Builder|Student whereId($value)
 * @method static Builder|Student whereOncampus($value)
 * @method static Builder|Student whereRemark($value)
 * @method static Builder|Student whereStudentNumber($value)
 * @method static Builder|Student whereUpdatedAt($value)
 * @method static Builder|Student whereUserId($value)
 * @mixin Eloquent
 */
class Student extends Model {
    
    use ModelTrait;
    
    const IMPORT_TITLES = [
        '姓名', '性别', '生日', '学校',
        '年级', '班级', '手机号码',
        '学号', '卡号', '住校',
        '备注', '监护关系',
    ];
    const EXPORT_TITLES = [
        '姓名', '性别', '班级', '学号',
        '卡号', '住校', '手机',
        '生日', '创建于', '更新于',
        '状态',
    ];
    const EXPORT_RANGES = [
        'class' => 0,
        'grade' => 1,
        'all'   => 2,
    ];
    
    protected $fillable = [
        'user_id', 'class_id', 'student_number',
        'card_number', 'oncampus', 'birthday',
        'remark', 'enabled',
    ];
    
    /**
     * 返回指定学生所属的班级对象
     *
     * @return BelongsTo
     */
    function squad() { return $this->belongsTo('App\Models\Squad', 'class_id', 'id'); }
    
    /**
     * 返回指定学生所属的年级对象
     *
     * @param $id
     * @return Grade
     */
    function grade($id) { return $this->find($id)->squad->grade; }
    
    /**
     * 返回指定学生所属的学校对象
     *
     * @param $id
     * @return School
     */
    function school($id) { return $this->find($id)->squad->grade->school; }
    
    /**
     * 获取指定学生的所有监护人对象
     *
     * @return BelongsToMany
     */
    function custodians() {
        
        return $this->belongsToMany('App\Models\Custodian', 'custodians_students');
        
    }
    
    /**
     * 获取指定学生对应的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 获取指定学生所有的分数对象
     *
     * @return HasMany
     */
    function scores() { return $this->hasMany('App\Models\Score'); }
    
    /**
     * 获取指定学生所有的总分对象
     *
     * @return HasMany
     */
    function scoreTotals() { return $this->hasMany('App\Models\ScoreTotal'); }
    
    /**
     * 获取指定学生的所有消费/充值记录
     *
     * @return HasMany
     */
    function consumptions() { return $this->hasMany('App\Models\Consumption'); }
    
    /**
     * 返回指定学生订阅的所有增值应用模块
     *
     * @return BelongsToMany
     */
    function modules() { return $this->belongsToMany('App\Models\Module', 'modules_students'); }
    
    /**
     * 学生列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Student.id', 'dt' => 0],
            ['db' => 'User.realname as realname', 'dt' => 1],
            [
                'db' => 'User.avatar_url', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::avatar($d);
                }
            ],
            [
                'db'        => 'User.gender as gender', 'dt' => 3,
                'formatter' => function ($d) {
                    return Snippet::gender($d);
                },
            ],
            [
                'db'        => 'Student.class_id', 'dt' => 4,
                'formatter' => function ($d) {
                    return Snippet::squad(Squad::find($d)->name);
                },
            ],
            ['db' => 'Student.student_number', 'dt' => 5],
            ['db' => 'Student.card_number', 'dt' => 6],
            [
                'db'        => 'Student.oncampus', 'dt' => 7,
                'formatter' => function ($d) {
                    return $d == 1 ? '是' : '否';
                },
            ],
            [
                'db'        => 'Student.birthday', 'dt' => 8, 'dr' => true,
                'formatter' => function ($d) {
                    return $d ? substr($d, 0, 10) : '';
                },
            ],
            ['db' => 'Student.created_at', 'dt' => 9, 'dr' => true],
            ['db' => 'Student.updated_at', 'dt' => 10, 'dr' => true],
            [
                'db'        => 'Student.enabled', 'dt' => 11,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
            ['db' => 'User.synced', 'dt' => 12],
            ['db' => 'User.subscribed', 'dt' => 13],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id',
                ],
            ]
        ];

        return Datatable::simple(
            $this->getModel(), $columns, $joins,
            'Student.user_id IN (' . $this->visibleUserIds() . ')'
        );
        
    }
    
    /**
     * 保存新创建的学生记录
     *
     * @param array $data
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建用户
                $user = User::create($data['user']);
                # 创建学籍
                $data['user_id'] = $user->id;
                $student = $this->create($data);
                # 保存手机号码
                (new Mobile)->store($data['mobile'], $user->id);
                # 保存用户所处部门
                (new DepartmentUser)->store($student->user_id, $student->squad->department_id);
                # 创建企业号成员
                $user->sync($user->id, 'create');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新指定的学生记录
     *
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        if (!$id) { return $this->batchUpdateContact($this); }
        try {
            DB::transaction(function () use ($data, $id) {
                $student = $this->find($id);
                # 更新用户
                User::find($student->user_id)->update($data['user']);
                # 更新学籍
                $student->update($data);
                # 更新手机号码
                (new Mobile)->store($data['mobile'], $student->user_id);
                # 更新用户所在部门
                (new DepartmentUser)->store($student->user_id, $student->squad->department_id);
                # 更新企业号成员
                $student->user->sync($student->user_id, 'update');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除指定的学生记录
     *
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function remove($id = null) {
        
        return (new User)->clean($this, $id);
        
    }
    
    /**
     * 删除指定学生的所有数据
     *
     * @param $id
     * @param bool $broadcast
     * @return bool
     * @throws Throwable
     */
    function purge($id, $broadcast = true) {
        
        try {
            DB::transaction(function () use ($id, $broadcast) {
                $student = $this->find($id);
                Consumption::whereStudentId($id)->delete();
                CustodianStudent::whereStudentId($id)->delete();
                ScoreTotal::whereStudentId($id)->delete();
                Score::whereStudentId($id)->delete();
                StudentAttendance::whereStudentId($id)->delete();
                (new User)->remove($student->user_id);
                $student->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 导入学籍
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    function import() {
    
        abort_if(
            Request::method() != 'POST',
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        $file = Request::file('file');
        abort_if(
            empty($file) || !$file->isValid(),
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.empty_file')
        );
        
        return $this->upload($file);
    
    }
    
    /**
     * 上传学籍excel文件
     *
     * @param UploadedFile $file
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    function upload(UploadedFile $file) {
        
        $ext = $file->getClientOriginalExtension();     // 扩展名//xls
        $realPath = $file->getRealPath();   // 临时文件的绝对路径
        // 上传文件
        $filename = uniqid() . '.' . $ext;
        $stored = Storage::disk('uploads')->put(
            date('Y/m/d/') . $filename,
            file_get_contents($realPath)
        );
        abort_if(
            !$stored,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        $spreadsheet = IOFactory::load(
            $this->uploadedFilePath($filename)
        );
        $students = $spreadsheet->getActiveSheet()->toArray(
            null, true, true, true
        );
        abort_if(
            !empty(array_diff(self::IMPORT_TITLES, $students[1])),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.invalid_file_format')
        );
        $students = array_filter(
            array_values(
                array_shift($students)
            ), 'sizeof'
        );
        list($sns, $cns) = array_map(
            function ($ns) {
                foreach ($ns as $n => $count) { if ($count > 1) { $ds[] = $n; } }
                return $ds ?? [];
            }, array_map(
                function ($students, $col) {
                    return array_count_values(array_pluck($students, $col));
                }, [$students, $students], ['H', 'I']
            )
        );
        abort_if(
            !empty($sns) || !empty($cns),
            HttpStatusCode::NOT_ACCEPTABLE,
            (!empty($sns) ? ('学号: ' . implode(',', $sns)) : '') .
            (!empty($cns) ? ('卡号: ' . implode(',', $cns)) : '') .
            '有重复，请检查后重试'
        );
        
        ImportStudent::dispatch($students, Auth::id());
        Storage::disk('uploads')->delete($filename);
        
        return true;
        
    }
    
    /**
     * 导出学籍
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws ReflectionException
     */
    function export() {
        
        $range = Request::query('range');
        $id = Request::query('id');
        abort_if(
            !in_array($range, array_values(self::EXPORT_RANGES)),
            HttpstatusCode::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $students = null;
        switch ($range) {
            case self::EXPORT_RANGES['class']:
                $students = Squad::find($id)->students;
                break;
            case self::EXPORT_RANGES['grade']:
                $students = Grade::find($id)->students;
                break;
            case self::EXPORT_RANGES['all']:
                $students = $this->whereIn('id', $this->contactIds('student'))->get();
                break;
            default:
                break;
        }
        $records = [self::EXPORT_TITLES];
        foreach ($students as $student) {
            if (!$student->user) continue;
            $records[] = [
                $student->user->realname,
                $student->user->gender ? '男' : '女',
                $student->squad->name,
                $student->student_number,
                $student->card_number . "\t",
                $student->oncampus ? '是' : '否',
                implode(', ', $student->user->mobiles->pluck('mobile')->toArray()),
                $student->birthday,
                $student->created_at->toDateTimeString(),
                $student->updated_at->toDateTimeString(),
                $student->enabled ? '启用' : '禁用',
            ];
        }
        
        return $this->excel($records);
        
    }
    
    /**
     * 获取指定年级对应的班级列表
     *
     * @return JsonResponse
     */
    function classList() {
        
        list($classes) = (new Grade)->classList(
            Request::input('id')
        );
        $result['html']['classes'] = $classes;
        
        return response()->json($result);
        
    }
    
    /**
     * 返回年级和班级列表
     *
     * @param int $gradeId
     * @return array
     */
    function gcList($gradeId = 0): array {
        
        $grades = Grade::whereEnabled(1)
            ->where('school_id', $this->schoolId())
            ->pluck('name', 'id')
            ->toArray();
        if (empty($grades)) {
            $classes = [];
        } else {
            $gradeId = $gradeId == 0 ? array_keys($grades)[0] : $gradeId;
            $classes = Squad::whereEnabled(1)
                ->where('grade_id', $gradeId)
                ->pluck('name', 'id')
                ->toArray();
        }
        
        return [$grades, $classes];
        
    }
    
    /**
     * 返回指定学生参加的所有考试
     *
     * @param $id
     * @return array
     */
    function exams($id) {
        
        $student = $this->find($id);
        
        return (new Exam())->where('enabled', 1)->orderBy('start_date', 'desc')
            ->whereRaw('FIND_IN_SET(' . $student->class_id . ', class_ids)')
            ->get()->toArray();
        
    }
    
    /**
     * 返回create/edit view所需数据
     *
     * @return array
     */
    function compose() {
    
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        if (Request::route('id')) {
            $gradeId = $this->find(Request::route('id'))->squad->grade_id;
        } else {
            reset($grades);
            $gradeId = key($grades);
        }
        if (empty($grades)) {
            $classes = Squad::whereIn('id', $this->classIds())
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $classes = Squad::whereGradeId($gradeId)
                ->where('enabled', 1)
                ->pluck('name', 'id')
                ->toArray();
        }
        if (Request::route('id')) {
            $student = $this->find(Request::route('id'));
            $user = $student->user;
            $mobiles = $student->user->mobiles;
        }
        
        return [
            $grades, $classes, $user ?? null, $mobiles ?? null
        ];
        
    }
    
}
