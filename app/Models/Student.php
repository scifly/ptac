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
use Illuminate\Support\{Facades\Auth, Facades\DB, Facades\Request};
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
    
    const EXCEL_TITLES = [
        '姓名', '性别', '生日', '学校',
        '年级', '班级', '学号', '卡号',
        '住校', '备注', '监护关系',
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
                # 创建用户
                $user = User::create($data['user']);
                # 创建学籍
                $data['user_id'] = $user->id;
                $student = $this->create($data);
                # 保存手机号码
                (new Mobile)->store($data['mobile'], $user->id);
                # 保存用户所处部门
                (new DepartmentUser)->store($student->user_id, $student->squad->department_id);
                # 创建企业微信会员
                // $user->sync([[$user->id, '学生', 'create']]);
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
        
        if (!$id) {
            return $this->batchUpdateContact($this);
        }
        try {
            DB::transaction(function () use ($data, $id) {
                if ($id) {
                    $student = $this->find($id);
                    # 更新用户
                    $student->user->update($data['user']);
                    # 更新学籍
                    $student->update($data);
                    # 更新手机号码
                    (new Mobile)->store($data['mobile'], $student->user_id);
                    # 更新用户所在部门
                    (new DepartmentUser)->store($student->user_id, $student->squad->department_id);
                    # 更新企业号成员
                    // $userIds = [$student->user_id];
                } else {
                    $this->batchUpdateContact($this);
                    // $ids = array_values(Request::input('ids'));
                    // $userIds = $this->whereIn('id', $ids)->pluck('user_id')->toArray();
                }
                # 同步企业微信
                // (new User)->sync(array_map(
                //     function ($userId) {
                //         return [$userId, '学生', 'update'];
                //     }, $userIds)
                // );
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
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $student = $this->find($id);
                Consumption::whereStudentId($id)->delete();
                CustodianStudent::whereStudentId($id)->delete();
                ScoreTotal::whereStudentId($id)->delete();
                Score::whereStudentId($id)->delete();
                StudentAttendance::whereStudentId($id)->delete();
                (new User)->purge($student->user_id);
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
        $students = collect([]);
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
        $records = [];
        foreach ($students as $student) {
            if (!$student->user) continue;
            $cses = CustodianStudent::whereStudentId($student->id)->get();
            $relationships = [];
            foreach ($cses as $cs) {
                if (!$cs->custodian) continue;
                $cUser = $cs->custodian->user;
                $relationships[] = implode(':', [
                    $cs->relationship, $cUser->realname, $cUser->gender ? '男' : '女',
                    $cUser->mobiles->where('isdefault', 1)->first()->mobile,
                ]);
            }
            $sUser = $student->user;
            $sMobile = $sUser->mobiles->where('isdefault', 1)->first();
            $records[] = [
                $sUser->realname,
                $sUser->gender ? '男' : '女',
                date('Y-m-d', strtotime($student->birthday)),
                $student->squad->grade->school->name,
                $student->squad->grade->name,
                $student->squad->name,
                $sMobile ? $sMobile->mobile : '',
                $student->student_number,
                $student->card_number . "\t",
                $student->oncampus ? '住读' : '走读',
                $student->remark,
                !empty($relationships) ? implode(',', $relationships) : '',
            ];
        }
        usort($records, function ($a, $b) {
            return strcmp($a[4], $b[4])     # 按年级排序
                ?: strcmp($a[5], $b[5])     # 按班级排序
                    ?: strcmp($a[7], $b[7]);    # 按学号排序
        });
        
        return $this->excel(
            array_merge([self::EXCEL_TITLES], $records)
        );
        
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
            $grades, $classes, $user ?? null, $mobiles ?? null,
        ];
        
    }
    
}
