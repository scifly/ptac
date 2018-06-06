<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\GradeRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ReflectionException;
use Throwable;

/**
 * App\Models\Grade 年级
 *
 * @property int $id
 * @property string $name 年级名称
 * @property int $school_id 所属学校ID
 * @property string $educator_ids 年级主任教职员工ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $department_id 对应的部门ID
 * @property-read Collection|Squad[] $classes
 * @property-read Department $department
 * @property-read School $school
 * @property-read Collection|StudentAttendanceSetting[] $studentAttendanceSetting
 * @property-read Collection|Student[] $students
 * @method static Builder|Grade whereCreatedAt($value)
 * @method static Builder|Grade whereDepartmentId($value)
 * @method static Builder|Grade whereEducatorIds($value)
 * @method static Builder|Grade whereEnabled($value)
 * @method static Builder|Grade whereId($value)
 * @method static Builder|Grade whereName($value)
 * @method static Builder|Grade whereSchoolId($value)
 * @method static Builder|Grade whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Grade extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'school_id', 'department_id',
        'educator_ids', 'enabled',
    ];
    
    /**
     * 返回对应的部门对象
     *
     * @return BelongsTo
     */
    function department() { return $this->belongsTo('App\Models\Department'); }
    
    /**
     * 返回指定年级所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定年级包含的所有班级对象
     *
     * @return HasMany
     */
    function classes() { return $this->hasMany('App\Models\Squad'); }
    
    /**
     * 获取指定年级包含的学生考勤设置对象
     *
     * @return HasMany
     */
    function studentAttendanceSetting() { return $this->hasMany('App\Models\StudentAttendanceSetting'); }
    
    /**
     * 通过Squad中间对象获取指定年级包含的所有学生对象
     *
     * @return HasManyThrough
     */
    function students() {
        
        return $this->hasManyThrough(
            'App\Models\Student',
            'App\Models\Squad',
            'grade_id',
            'class_id');
        
    }
    
    /**
     * 根据学校ID返回年级列表(id, name)
     *
     * @return Collection
     */
    function gradeList() {
        
        return self::whereIn('id', $this->gradeIds())
            ->get()->pluck('name', 'id');
        
    }
    
    /**
     * 保存年级
     *
     * @param GradeRequest $request
     * @return bool
     * @throws Exception
     */
    function store(GradeRequest $request) {
        
        $grade = null;
        try {
            DB::transaction(function () use ($request, &$grade) {
                # 创建年级、对应的部门
                $grade = $this->create($request->all());
                $department = (new Department())->storeDepartment($grade, 'school');
                # 更新“年级”的部门id
                $grade->update([
                    'department_id' => $department->id,
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $grade;
        
    }
    
    /**
     * 更新年级
     *
     * @param GradeRequest $request
     * @param $id
     * @return bool
     * @throws Exception
     */
    function modify(GradeRequest $request, $id) {
        
        $grade = null;
        try {
            DB::transaction(function () use ($request, $id, &$grade) {
                $grade = $this->find($id);
                $grade->update($request->all());
                (new Department())->modifyDepartment($this->find($id));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $grade ? $this->find($id) : null;
        
    }
    
    /**
     * 删除年级
     *
     * @param $id
     * @return bool
     * @throws ReflectionException
     * @throws Throwable
     */
    function remove($id) {
        
        $grade = self::find($id);
        if ($this->removable($grade)) {
            try {
                DB::transaction(function () use ($grade) {
                    (new Department())->removeDepartment($grade);
                    $grade->delete();
                });
            } catch (Exception $e) {
                throw $e;
            }
            return true;
        }
        
        return false;
        
    }
    
    /**
     * 返回指定年级包含的班级列表html
     *
     * @param $id
     * @return array
     */
    function classList($id) {
        
        abort_if(
            !in_array($id, $this->gradeIds()) || !$this->find($id),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $items = $this->find($id)->classes->pluck('name', 'id')->toArray();
        reset($items);
        
        return [
            $this->singleSelectList($items, 'class_id'),
            key($items),
        ];
        
    }
    
    /**
     * 返回对指定用户(教职员工)可见的所有年级类部门对象
     *
     * @param null $userId
     * @return Department[]|Collection
     */
    function departments($userId = null) {
        
        $user = $userId ? User::find($userId) : Auth::user();
        // abort_if(
        //     !$user->educator,
        //     HttpStatusCode::UNAUTHORIZED,
        //     __('messages.unauthorized')
        // );
        $ids = $this->gradeIds(session('schoolId'), $user->id);
        $departmentIds = $this->whereIn('id', $ids)->pluck('department_id')->toArray();
        
        return Department::whereIn('id', $departmentIds)->get();
        
    }
    
    /**
     * 年级列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Grade.id', 'dt' => 0],
            [
                'db'        => 'Grade.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-object-group', '') . $d;
                },
            ],
            [
                'db'        => 'Grade.educator_ids', 'dt' => 2,
                'formatter' => function ($d) {
                    if (empty($d)) {
                        return '';
                    }
                    $educatorIds = explode(',', $d);
                    $educators = [];
                    foreach ($educatorIds as $id) {
                        $educator = Educator::find($id);
                        if (!empty($educator) && $educator->user) {
                            $educators[] = $educator->user->realname;
                        }
                    }
                    
                    return implode(', ', $educators);
                },
            ],
            ['db' => 'Grade.created_at', 'dt' => 3],
            ['db' => 'Grade.updated_at', 'dt' => 4],
            [
                'db'        => 'Grade.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return $this->syncStatus($d, $row);
                },
            ],
            ['db' => 'Department.synced as synced', 'dt' => 6]
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Grade.school_id'
                ]
            ],
            [
                'table' => 'departments',
                'alias' => 'Department',
                'type' => 'INNER',
                'conditions' => [
                    'Department.id = Grade.department_id'
                ]
            ]
        ];
        $condition = 'School.id = ' . $this->schoolId();
        if (!in_array(Auth::user()->group->name, Constant::SUPER_ROLES)) {
            $condition .= ' AND Grade.id IN (' . implode(',', $this->gradeIds()) . ')';
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
}