<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\SquadRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
 * @property-read Department $department
 * @property-read Collection|Educator[] $educators
 * @property-read Grade $grade
 * @property-read Collection|Student[] $students
 * @method static Builder|Squad whereCreatedAt($value)
 * @method static Builder|Squad whereDepartmentId($value)
 * @method static Builder|Squad whereEducatorIds($value)
 * @method static Builder|Squad whereEnabled($value)
 * @method static Builder|Squad whereGradeId($value)
 * @method static Builder|Squad whereId($value)
 * @method static Builder|Squad whereName($value)
 * @method static Builder|Squad whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subject[] $subjects
 */
class Squad extends Model {
    
    use ModelTrait;
    
    protected $table = 'classes';
    
    protected $fillable = [
        'id', 'grade_id', 'name', 'department_id',
        'educator_ids', 'enabled',
    ];
    
    /**
     * 返回对应的部门对象
     *
     * @return BelongsTo
     */
    function department() { return $this->belongsTo('App\Models\Department'); }
    
    /**
     * 返回指定班级所属的年级对象
     *
     * @return BelongsTo
     */
    function grade() { return $this->belongsTo('App\Models\Grade'); }
    
    /**
     * 获取指定班级包含的所有学生对象
     *
     * @return HasMany
     */
    function students() { return $this->hasMany('App\Models\Student', 'class_id'); }
    
    /**
     * 获取指定班级包含的所有教职员工对象
     *
     * @return BelongsToMany
     */
    function educators() { return $this->belongsToMany('App\Models\Educator', 'educators_classes'); }
    
    /**
     * 获取指定班级对应的所有科目对象
     *
     * @return BelongsToMany
     */
    function subjects() {
        
        return $this->belongsToMany(
            'App\Models\Subject',
            'educators_classes',
            'class_id',
            'subject_id'
        );
        
    }
    
    /**
     * 班级列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Squad.id', 'dt' => 0],
            [
                'db'        => 'Squad.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return Snippet::squad($d);
                },
            ],
            [
                'db'        => 'Grade.name as gradename', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::grade($d);
                },
            ],
            [
                'db'        => 'Squad.educator_ids', 'dt' => 3,
                'formatter' => function ($d) {
                    if (empty($d)) {
                        return '';
                    }
                    $educatorIds = explode(',', $d);
                    $educators = [];
                    foreach ($educatorIds as $id) {
                        $educator = Educator::find($id);
                        if ($educator) {
                            if ($educator->user) {
                                $educators[] = $educator->user->realname;
                            }
                        }
                        
                    }
                    
                    return implode(', ', $educators);
                },
            ],
            ['db' => 'Squad.created_at', 'dt' => 4],
            ['db' => 'Squad.updated_at', 'dt' => 5],
            [
                'db'        => 'Squad.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return $this->syncStatus($d, $row, false);
                },
            ],
            ['db' => 'Department.synced as synced', 'dt' => 7],
        ];
        $joins = [
            [
                'table'      => 'grades',
                'alias'      => 'Grade',
                'type'       => 'INNER',
                'conditions' => [
                    'Grade.id = Squad.grade_id',
                ],
            ],
            [
                'table'      => 'departments',
                'alias'      => 'Department',
                'type'       => 'INNER',
                'conditions' => [
                    'Department.id = Squad.department_id',
                ],
            ],
        ];
        $condition = 'Squad.id IN (' . implode(',', $this->classIds()) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存班级
     *
     * @param SquadRequest $request
     * @return bool
     * @throws Throwable
     */
    function store(SquadRequest $request) {
        
        $class = null;
        try {
            DB::transaction(function () use ($request, &$class) {
                # 创建班级、对应的部门
                $class = $this->create($request->all());
                $department = (new Department)->storeDepartment($class, 'grade');
                # 更新“班级”的部门id
                $class->update([
                    'department_id' => $department->id,
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $class;
        
    }
    
    /**
     * 更新班级
     *
     * @param SquadRequest $request
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(SquadRequest $request, $id = null) {
        
        if (!$id) {
            return $this->batch($this);
        }
        $class = null;
        try {
            DB::transaction(function () use ($request, $id, &$class) {
                $class = $this->find($id);
                $class->update($request->all());
                (new Department)->modifyDepartment($this->find($id));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $class ? $this->find($id) : null;
        
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定班级的所有相关数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $class = $this->find($id);
                $this->delRelated('class_id', 'Student', $id);
                (new Department)->remove($class->department_id);
                $class->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新指定教职员工的班级主任任职记录
     *
     * @param $educatorId
     * @return bool
     * @throws Throwable
     */
    function removeEducator($educatorId) {
        
        try {
            DB::transaction(function () use ($educatorId) {
                $classes = $this->whereRaw($educatorId . ' IN (educator_ids)')->get();
                foreach ($classes as $class) {
                    $educatorIds = array_diff(explode(',', $class->educator_ids), [$educatorId]);
                    $class->update(['educator_ids' => implode(',', $educatorIds)]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回指定班级包含的学生列表html
     *
     * @param $id
     * @return string
     */
    function studentList($id) {
        
        abort_if(
            isset($id) && (!in_array($id, $this->classIds()) || !$this->find($id)),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $class = $this->find($id);
        $students = $class ? $class->students : [];
        $items = [];
        foreach ($students as $student) {
            if (!$student->user) {
                continue;
            }
            $items[$student->id] = $student->user->realname .
                '(' . $student->student_number . ')';
        }
        
        return response()->json([
            'html' => $this->singleSelectList($items, 'student_id'),
        ]);
        
    }
    
    /**
     * 返回对指定用户(教职员工)可见的所有班级类部门对象
     *
     * @param null $userId
     * @return Collection|Department[]
     */
    function departments($userId = null) {
        
        $user = $userId ? User::find($userId) : Auth::user();
        // abort_if(
        //     !$user->educator,
        //     HttpStatusCode::UNAUTHORIZED,
        //     __('messages.unauthorized')
        // );
        $ids = $this->classIds(session('schoolId'), $user->id);
        $departmentIds = $this->whereIn('id', $ids)->pluck('department_id')->toArray();
        
        return Department::whereIn('id', $departmentIds)->get();
        
    }
    
}
