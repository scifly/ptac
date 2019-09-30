<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};
use Illuminate\Support\Facades\{Auth, DB, Request};
use ReflectionException;
use Throwable;

/**
 * App\Models\Squad 班级
 *
 * @property int $id
 * @property int $grade_id 所属年级ID
 * @property string $name 班级名称
 * @property string $educator_ids 班主任教职员工ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $department_id 对应的部门ID
 * @property-read Department $dept
 * @property-read Collection|Educator[] $educators
 * @property-read Grade $grade
 * @property-read Collection|Student[] $students
 * @property-read Collection|Subject[] $subjects
 * @property-read int|null $educators_count
 * @property-read int|null $students_count
 * @property-read int|null $subjects_count
 * @method static Builder|Squad whereCreatedAt($value)
 * @method static Builder|Squad whereDepartmentId($value)
 * @method static Builder|Squad whereEducatorIds($value)
 * @method static Builder|Squad whereEnabled($value)
 * @method static Builder|Squad whereGradeId($value)
 * @method static Builder|Squad whereId($value)
 * @method static Builder|Squad whereName($value)
 * @method static Builder|Squad whereUpdatedAt($value)
 * @method static Builder|Squad newModelQuery()
 * @method static Builder|Squad newQuery()
 * @method static Builder|Squad query()
 * @mixin Eloquent
 */
class Squad extends Model {
    
    use ModelTrait;
    
    protected $table = 'classes';
    
    protected $fillable = [
        'id', 'grade_id', 'name', 'department_id',
        'educator_ids', 'enabled',
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function dept() { return $this->belongsTo('App\Models\Department', 'department_id'); }
    
    /** @return BelongsTo */
    function grade() { return $this->belongsTo('App\Models\Grade'); }
    
    /** @return HasMany */
    function students() { return $this->hasMany('App\Models\Student', 'class_id'); }
    
    /** @return BelongsToMany */
    function educators() { return $this->belongsToMany('App\Models\Educator', 'class_educator'); }
    
    /** @return BelongsToMany */
    function subjects() {
        
        return $this->belongsToMany(
            'App\Models\Subject',
            'class_educator',
            'class_id',
            'subject_id'
        );
        
    }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 班级列表
     *
     * @return array
     * @throws Exception
     */
    function index() {
        
        $columns = [
            ['db' => 'Squad.id', 'dt' => 0],
            [
                'db'        => 'Squad.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'squad');
                },
            ],
            [
                'db'        => 'Grade.name as gname', 'dt' => 2,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'grade');
                },
            ],
            [
                'db'        => 'Squad.educator_ids', 'dt' => 3,
                'formatter' => function ($d) {
                    return Educator::with('user')
                        ->whereIn('id', explode(',', $d))
                        ->get()->pluck('user.realname')->join(', ');
                },
            ],
            ['db' => 'Squad.created_at', 'dt' => 4],
            ['db' => 'Squad.updated_at', 'dt' => 5],
            [
                'db'        => 'Department.synced', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d ? '是' : '否';
                },
            ],
            [
                'db'        => 'Squad.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'departments',
                'alias'      => 'Department',
                'type'       => 'INNER',
                'conditions' => [
                    'Department.id = Squad.department_id',
                ],
            ],
            [
                'table'      => 'grades',
                'alias'      => 'Grade',
                'type'       => 'INNER',
                'conditions' => [
                    'Grade.id = Squad.grade_id',
                ],
            ],
        ];
        $condition = 'Squad.id IN (' . $this->classIds()->join(',') . ')';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存班级
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建班级对应的部门
                $class = $this->create($data);
                # 更新“班级”的部门id
                $department = (new Department)->stow($class, 'grade');
                $class->update(['department_id' => $department->id]);
                # 更新部门标签绑定关系
                (new DepartmentTag)->storeByDeptId(
                    $department->id, $data['tag_ids'] ?? []
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新班级
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        return $this->revise(
            $this, $data, $id,
            function (Squad $class) use ($data) {
                (new Department)->alter($class);
                # 更新部门标签绑定关系
                (new DepartmentTag)->storeByDeptId(
                    $class->department_id, $data['tag_ids'] ?? []
                );
            }
        );
        // try {
        //     DB::transaction(function () use ($data, $id) {
        //         if ($class = $this->find($id)) {
        //             # 更新班级
        //             $class->update($data);
        //             (new Department)->alter($class);
        //             # 更新部门标签绑定关系
        //             (new DepartmentTag)->storeByDeptId(
        //                 $class->department_id, $data['tag_ids'] ?? []
        //             );
        //         } else {
        //             $this->batch($this);
        //         }
        //     });
        // } catch (Exception $e) {
        //     throw $e;
        // }
        //
        // return true;
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'purge.class_id'  => ['Student', 'ClassEducator'],
            'clear.class_ids' => ['Exam'],
        ]);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回view所需数据
     *
     * @return array
     * @throws ReflectionException
     * @throws Exception
     * @throws Exception
     */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $nil = collect([null => '全部']);
            
            return [
                'titles' => [
                    '#', '名称',
                    [
                        'title' => '所属年级',
                        'html'  => $this->htmlSelect(
                            $nil->union(
                                Grade::whereIn('id', $this->gradeIds())->pluck('name', 'id')
                            ),
                            'filter_grade_id'
                        ),
                    ],
                    '班主任',
                    [
                        'title' => '创建于',
                        'html'  => $this->htmlDTRange('创建于'),
                    ],
                    [
                        'title' => '更新于',
                        'html'  => $this->htmlDTRange('更新于'),
                    ],
                    [
                        'title' => '同步状态',
                        'html'  => $this->htmlSelect(
                            $nil->union(['未同步', '已同步']), 'filter_subscribed'
                        ),
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->htmlSelect(
                            $nil->union(['未启用', '已启用']), 'filter_enabled'
                        ),
                    ],
                ],
                'filter' => true,
            ];
        } else {
            $grades = Grade::whereIn('id', $this->gradeIds())
                ->where('enabled', 1)
                ->pluck('name', 'id');
            $educators = Educator::with('user')
                ->whereIn('id', $this->contactIds('educator'))
                ->where('enabled', 1)
                ->pluck('user.realname', 'id');
            $class = $this->find(Request::route('id'));
            $selectedEducators = collect(
                explode(',', $class ? $class->educator_ids : null)
            );
            
            return array_merge(
                [
                    'grades'            => $grades,
                    'educators'         => $educators,
                    'selectedEducators' => $selectedEducators,
                ],
                (new Tag)->compose('department', $class ? $class->dept : null)
            );
        }
        
    }
    
    /**
     * 返回指定班级包含的学生列表html
     *
     * @param $id
     * @return string
     * @throws Exception
     */
    function studentList($id) {
        
        abort_if(
            isset($id) && (!$this->classIds()->flip()->has($id) || !$this->find($id)),
            Constant::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $class = $this->find($id);
        $students = $class ? $class->students : [];
        foreach ($students as $student) {
            if (!$student->user) continue;
            $items[$student->id] = $student->user->realname . '(' . $student->sn . ')';
        }
        
        return $this->htmlSelect($items ?? [], 'student_id');
        
    }
    
    /**
     * 返回对指定用户(教职员工)可见的所有班级类部门对象
     *
     * @param null $userId
     * @return Collection|Department[]
     * @throws Exception
     */
    function departments($userId = null) {
        
        $user = User::find($userId ?? Auth::id());
        // abort_if(
        //     !$user->educator,
        //     Constant::UNAUTHORIZED,
        //     __('messages.unauthorized')
        // );
        $ids = $this->classIds(session('schoolId'), $user->id);
        $departmentIds = $this->whereIn('id', $ids)->pluck('department_id')->toArray();
        
        return Department::whereIn('id', $departmentIds)->get();
        
    }
    
}
