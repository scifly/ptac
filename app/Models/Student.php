<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use App\Jobs\ExportStudent;
use App\Jobs\ImportStudent;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Form;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\{Facades\Auth, Facades\DB, Facades\Request};
use ReflectionException;
use Throwable;

/**
 * App\Models\Student 学生
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $class_id 班级ID
 * @property string $sn 学号
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
 * @method static Builder|Student whereClassId($value)
 * @method static Builder|Student whereCreatedAt($value)
 * @method static Builder|Student whereEnabled($value)
 * @method static Builder|Student whereId($value)
 * @method static Builder|Student whereOncampus($value)
 * @method static Builder|Student whereRemark($value)
 * @method static Builder|Student whereSn($value)
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
        '年级', '班级', '学号', '住校',
        '备注', '监护关系',
    ];
    
    protected $fillable = [
        'user_id', 'class_id', 'sn', 'oncampus',
        'birthday', 'remark', 'enabled',
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
                    return Snippet::icon(Squad::find($d)->name, 'squad');
                },
            ],
            ['db' => 'Student.sn', 'dt' => 5],
            [
                'db'        => 'Student.oncampus', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d == 1 ? '是' : '否';
                },
            ],
            [
                'db'        => 'Student.birthday', 'dt' => 7, 'dr' => true,
                'formatter' => function ($d) {
                    return $d ? substr($d, 0, 10) : '';
                },
            ],
            ['db' => 'Student.created_at', 'dt' => 8, 'dr' => true],
            ['db' => 'Student.updated_at', 'dt' => 9, 'dr' => true],
            [
                'db'        => 'Student.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
            ['db' => 'User.synced', 'dt' => 11],
            ['db' => 'User.subscribed', 'dt' => 12],
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
                (new Card)->store($user);
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
                if (!$id) {
                    $this->batch($this);
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
                    # 更新一卡通
                    (new Card)->store($student->user);
                    $student->update($data);
                    (new DepartmentUser)->store(
                        $student->user_id,
                        $student->squad->department_id
                    );
                }
                empty($userIds = $userIds ?? []) ?: (new User)->sync(
                    array_map(
                        function ($userId) {
                            return [$userId, '监护人', 'update'];
                        }, $userIds
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
                foreach ($ids as $id) {
                    $student = $this->find($id);
                    $cIds = array_merge(
                        $cIds ?? [],
                        $student->custodians->pluck('id')->toArray()
                    );
                    $uIds[] = $student->user_id;
                }
                if (!empty($cIds = array_unique($cIds ?? []))) {
                    Request::replace(['ids' => $cIds]);
                    (new Custodian)->remove();
                }
                if (!empty($uIds = $uIds ?? [])) {
                    Request::replace(['ids' => $uIds]);
                    (new User)->remove();
                }
                Request::replace(['ids' => $ids]);
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
        $ns = array_count_values(
            array_map('strval', array_pluck($records, 'G'))
        );
        foreach ($ns as $n => $count) {
            if (!empty($n) && $count > 1) $ds[] = $n;
        }
        $sns = $ds ?? [];
        abort_if(
            !empty($ds ?? []),
            HttpStatusCode::NOT_ACCEPTABLE,
            implode('', [
                (!empty($sns) ? ('学号: ' . implode(',', $sns)) : ''),
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
     * 批量发卡
     *
     * @return bool|string
     * @throws Throwable
     */
    function issue() {
    
        if (Request::has('classId')) {
            $students = Student::whereClassId(Request::input('classId'))->get();
            $snHtml = Form::text('sn', '%s', ['class' => 'form-control text-blue'])->toHtml();
            $record = '<tr><td>%s</td><td class="text-center">%s</td><td>' . $snHtml . '</td></tr>';
            $list = '';
            foreach ($students as $student) {
                $card = $student->user->card;
                $sn = $card ? $card->sn : null;
                $list .= sprintf(
                    $record, $student->user_id, $student->user->realname, $sn
                );
            }
            return $list;
        }
        try {
            DB::transaction(function () {
                foreach (Request::all() as $userId => $sn) {
                    $card = Card::updateOrCreate(
                        ['user_id' => $userId],
                        ['sn' => $sn, 'status' => 1]
                    );
                    $card->user->update(['card_id' => $card->id]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return response()->json([
            'title' => '批量发卡',
            'message' => __('messages.ok')
        ]);
    
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
            $student = $this->find(Request::route('id'));
            $student->{'card'} = $student->user->card;
            $student->{'grade_id'} = $student->squad->grade_id;
            $mobiles = $student->user->mobiles;
        }
        $gradeId = Request::route('id')
            ? $this->find(Request::route('id'))->squad->grade_id
            : key($grades);
        $builder = empty($grades)
            ? Squad::whereIn('id', $this->classIds())
            : Squad::where(['grade_id' => $gradeId, 'enabled' => 1]);
    
        return [
            $student ?? null,
            $grades,
            $builder->pluck('name', 'id')->toArray(),
            $mobiles ?? null,
        ];
        
    }
    
}
