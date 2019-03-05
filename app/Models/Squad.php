<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait, Snippet};
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
 * @property-read Collection|Subject[] $subjects
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
                    return Snippet::icon($d, 'squad');
                },
            ],
            [
                'db'        => 'Squad.grade_id', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::icon(Grade::find($d)->name, 'grade');
                },
            ],
            [
                'db'        => 'Squad.educator_ids', 'dt' => 3,
                'formatter' => function ($d) {
                    return implode(', ', Educator::whereIn('id', explode(',', $d))
                        ->with('user')->get()->pluck('user.realname')->toArray());
                },
            ],
            ['db' => 'Squad.created_at', 'dt' => 4],
            ['db' => 'Squad.updated_at', 'dt' => 5],
            [
                'db'        => 'Department.synced', 'dt' => 6,
                'formatter' => function ($d) {
                    return $this->synced($d);
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
        ];
        $condition = 'Squad.id IN (' . implode(',', $this->classIds()) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
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
                # 创建班级、对应的部门
                $class = $this->create($data);
                $department = (new Department)->stow($class, 'grade');
                # 更新“班级”的部门id
                $class->update(['department_id' => $department->id]);
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
        
        try {
            DB::transaction(function () use ($data, $id) {
                if ($id) {
                    $class = $this->find($id);
                    $class->update($data);
                    (new Department)->alter($this->find($id));
                } else {
                    $this->batch($this);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $studentIds = Student::whereIn('class_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $studentIds]);
                (new Student)->remove();
                $departmentIds = $this->whereIn('id', $ids)
                    ->pluck('department_id')->toArray();
                Request::replace(['ids' => $departmentIds]);
                (new Department)->remove();
                Request::replace(['ids' => $ids]);
                $this->purge(['Squad'], 'id');
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
            if (!$student->user) continue;
            $items[$student->id] = $student->user->realname . '(' . $student->sn . ')';
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
        
        $user = User::find($userId ?? Auth::id());
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
