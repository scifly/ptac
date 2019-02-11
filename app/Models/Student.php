<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use App\Jobs\ExportStudent;
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
use Illuminate\Support\{Facades\Auth, Facades\DB, Facades\Request};
use ReflectionClass;
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
 * @method static Builder|Student newModelQuery()
 * @method static Builder|Student newQuery()
 * @method static Builder|Student query()
 * @mixin Eloquent
 */
class Student extends Model {
    
    use ModelTrait;
    
    const EXCEL_TITLES = [
        '姓名', '性别', '学校', '生日',
        '年级', '班级', '学号', '卡号',
        '住校', '备注', '监护关系',
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
        
        return $this->belongsToMany(
            'App\Models\Custodian',
            'custodians_students',
            'student_id',
            'custodian_id'
        );
        
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
                'db'        => 'User.avatar_url', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::avatar($d);
                },
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
            ],
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
                $user = User::create($data['user']);
                $data['user_id'] = $user->id;
                $student = $this->create($data);
                (new DepartmentUser)->store(
                    $student->user_id,
                    $student->squad->department_id
                );
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
        
        try {
            DB::transaction(function () use ($data, $id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $userIds = [];
                if (!$id) {
                    $data = ['enable' => Request::input('action') == 'enable' ? 1 : 0];
                    $this->whereIn('id', $ids)->update($data);
                } else {
                    $student = $this->find($id);
                    # 如果学生班级发生变化，则需更新对应监护人的部门绑定关系
                    if ($student->class_id != $data['class_id']) {
                        $userIds = $student->custodians->pluck('user_id')->toArray();
                        $departmentId = Squad::find($data['class_id'])->department_id;
                        foreach ($student->custodians as $custodian) {
                            $condition = [
                                'user_id' => $custodian->user_id,
                                'department_id' => $student->squad->department_id
                            ];
                            if ($du = DepartmentUser::where($condition)->first()) {
                                $du->update(['department_id' => $departmentId]);
                            } else {
                                DepartmentUser::create(
                                    array_combine(Constant::DU_FIELDS, [
                                        $departmentId, $custodian->user_id, 0
                                    ])
                                );
                            }
                        }
                    }
                    $student->user->update($data['user']);
                    $student->update($data);
                    (new DepartmentUser)->store(
                        $student->user_id,
                        $student->squad->department_id
                    );
                }
                empty($userIds) ?: (new User)->sync(
                    array_map(
                        function ($userId) { return [$userId, '监护人', 'update']; }, $userIds
                    )
                );
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
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                # 更新对应监护人信息
                foreach ($ids as $id) {
                    $student = $this->find($id);
                    foreach ($student->custodians as $custodian) {
                        CustodianStudent::where([
                            'student_id' => $id,
                            'custodian_id' => $custodian->id
                        ])->delete();
                        DepartmentUser::where([
                            'department_id' => $student->squad->department_id,
                            'user_id' => $custodian->user_id,
                            'enabled' => 0
                        ])->delete();
                        $custodian->students->isNotEmpty()
                            ? $uUserIds[] = [$custodian->user_id, '监护人', 'update']
                            : $rUserIds[] = [$custodian->user_id, '监护人', 'delete'];
                    }
                }
                array_map(
                    function (array $contacts) { (new User)->sync($contacts); },
                    [$uUserIds ?? [], $rUserIds ?? []]
                );
                $this->purge([
                    class_basename($this), 'Consumption', 'CustodianStudent',
                    'ScoreTotal', 'Score', 'StudentAttendance'
                ], 'student_id');
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
    
        $records = $this->upload();
        list($sns, $cns) = array_map(
            function ($ns) {
                foreach ($ns as $n => $count) {
                    if (!empty($n) && $count > 1) $ds[] = $n;
                }
            
                return $ds ?? [];
            }, array_map(
                function ($students, $col) {
                    return array_count_values(
                        array_map('strval', array_pluck($students, $col))
                    );
                }, [$records, $records], ['G', 'H']
            )
        );
        abort_if(
            !empty($sns) || !empty($cns),
            HttpStatusCode::NOT_ACCEPTABLE,
            implode('', [
                (!empty($sns) ? ('学号: ' . implode(',', $sns)) : ''),
                (!empty($cns) ? ('卡号: ' . implode(',', $cns)) : ''),
                '有重复，请检查后重试'
            ])
        );
        ImportStudent::dispatch($records, Auth::id());
    
        return true;
        
    }
    
    /**
     * 导出学籍
     *
     * @return mixed
     * @throws ReflectionException
     */
    function export() {
        
        $id = Request::input('id');
        abort_if(
            !in_array(
                $range = Request::input('range'),
                array_values(Constant::EXPORT_RANGES)
            ),
            HttpstatusCode::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $students = collect([]);
        switch ($range) {
            case Constant::EXPORT_RANGES['class']:
                $students = Squad::find($id)->students;
                break;
            case Constant::EXPORT_RANGES['grade']:
                $students = Grade::find($id)->students;
                break;
            case Constant::EXPORT_RANGES['all']:
                $students = $this->whereIn('id', $this->contactIds('student'))->get();
                break;
            default:
                break;
        }
        ExportStudent::dispatch($students, self::EXCEL_TITLES, Auth::id());
        
        return true;
        
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
     * 返回指定学生参加的所有考试
     *
     * @param $id
     * @return array
     */
    function exams($id) {
    
        return (new Exam)->whereRaw('FIND_IN_SET(' . $this->find($id)->class_id . ', class_ids)')
            ->orderBy('start_date', 'desc')
            ->where('enabled', 1)
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
            $grades, $classes, $user ?? null, $mobiles ?? null,
        ];
        
    }
    
}
