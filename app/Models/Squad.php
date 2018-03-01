<?php
namespace App\Models;

use App\Events\ClassCreated;
use App\Events\ClassDeleted;
use App\Events\ClassUpdated;
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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

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
     * 保存班级
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    function store(array $data, $fireEvent = false) {
        
        $class = self::create($data);
        if ($class && $fireEvent) {
            event(new ClassCreated($class));
            
            return true;
        }
        
        return $class ? true : false;
        
    }
    
    /**
     * 更新班级
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    function modify(array $data, $id, $fireEvent = false) {
        
        $class = self::find($id);
        $updated = $class->update($data);
        if ($updated && $fireEvent) {
            event(new ClassUpdated($class));
            
            return true;
        }
        
        return $updated ? true : false;
        
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @param bool $fireEvent
     * @return bool
     * @throws Exception
     */
    function remove($id, $fireEvent = false) {
        
        $class = self::find($id);
        if (!$class) {
            return false;
        }
        $removed = $this->removable($class) ? $class->delete() : false;
        if ($removed && $fireEvent) {
            event(new ClassDeleted($class));
            
            return true;
        }
        
        return $removed ? true : false;
        
    }
    
    /**
     * 获取对当前用户可见的所有班级Id
     *
     * @return array
     */
    function classIds() {
    
        $user = Auth::user();
        $role = $user->group->name;
        if (in_array($role, Constant::SUPER_ROLES)) {
            $schoolId = $this->schoolId();
            $grades = School::find($schoolId)->grades;
            $classIds = [];
            foreach ($grades as $grade) {
                $classes = $grade->classes;
                foreach ($classes as $class) {
                    $classIds[] = $class->id;
                }
            }
        } else {
            $departmentIds = $this->departmentIds($user->id);
            $classIds = [];
            
            foreach ($departmentIds as $id) {
                $department = Department::find($id);
                if ($department->departmentType->name == '班级') {
                    $classIds[] = $department->squad->id;
                }
            }
        }
        
        return empty($classIds) ? [0] : $classIds;
    
    }
    
    /**
     * 班级列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Squad.id', 'dt' => 0],
            [
                'db'        => 'Squad.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return '<i class="fa fa-users"></i>&nbsp;' . $d;
                },
            ],
            [
                'db'        => 'Grade.name as gradename', 'dt' => 2,
                'formatter' => function ($d) {
                    return '<i class="fa fa-object-group"></i>&nbsp;' . $d;
                },
            ],
            [
                'db'        => 'School.name as schoolname', 'dt' => 3,
                'formatter' => function ($d) {
                    return '<i class="fa fa-university"></i>&nbsp;' . $d;
                },
            ],
            [
                'db'        => 'Squad.educator_ids', 'dt' => 4,
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
            ['db' => 'Squad.created_at', 'dt' => 5],
            ['db' => 'Squad.updated_at', 'dt' => 6],
            [
                'db'        => 'Squad.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
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
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Grade.school_id',
                ],
            ],
        ];
        $condition = 'Squad.id IN (' . implode(',', $this->classIds()) . ')';
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);
        
    }
    
}
