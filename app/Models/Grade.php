<?php
namespace App\Models;

use App\Events\GradeCreated;
use App\Events\GradeDeleted;
use App\Events\GradeUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
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
            'id',
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
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    function store(array $data, $fireEvent = false) {
        
        $grade = self::create($data);
        if ($grade && $fireEvent) {
            event(new GradeCreated($grade));
            
            return true;
        }
        
        return $grade ? true : false;
        
    }
    
    /**
     * 更新年级
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    function modify(array $data, $id, $fireEvent = false) {
        
        $grade = self::find($id);
        $updated = $grade->update($data);
        if ($updated && $fireEvent) {
            event(new GradeUpdated($grade));
            
            return true;
        }
        
        return $updated ? true : false;
        
    }
    
    /**
     * 删除年级
     *
     * @param $id
     * @param bool $fireEvent
     * @return bool
     * @throws Exception
     */
    function remove($id, $fireEvent = false) {
        
        $grade = self::find($id);
        if (!$grade) {
            return false;
        }
        $removed = self::removable($grade) ? $grade->delete() : false;
        if ($removed && $fireEvent) {
            event(new GradeDeleted($grade));
            
            return true;
        }
        
        return $removed ? true : false;
        
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
                    return '<i class="fa fa-object-group"></i>&nbsp;' . $d;
                },
            ],
            [
                'db'        => 'School.name as schoolname', 'dt' => 2,
                'formatter' => function ($d) {
                    return '<i class="fa fa-university"></i>&nbsp;' . $d;
                },
            ],
            [
                'db'        => 'Grade.educator_ids', 'dt' => 3,
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
            ['db' => 'Grade.created_at', 'dt' => 4],
            ['db' => 'Grade.updated_at', 'dt' => 5],
            [
                'db'        => 'Grade.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Grade.school_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        if (!in_array(Auth::user()->group->name, Constant::SUPER_ROLES)) {
            $condition .= ' AND Grade.id IN (' . implode(',', $this->gradeIds()) . ')';
        }
        
        return Datatable::simple(
            self::getModel(), $columns, $joins, $condition
        );
        
    }
    
}
