<?php
namespace App\Models;

use App\Events\ClassCreated;
use App\Events\ClassDeleted;
use App\Events\ClassUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Squad 班级
 *
 * @property int $id
 * @property int $grade_id 所属年级ID
 * @property string $name 班级名称
 * @property string $educator_ids 班主任教职员工ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Squad whereCreatedAt($value)
 * @method static Builder|Squad whereEducatorIds($value)
 * @method static Builder|Squad whereEnabled($value)
 * @method static Builder|Squad whereGradeId($value)
 * @method static Builder|Squad whereId($value)
 * @method static Builder|Squad whereName($value)
 * @method static Builder|Squad whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Grade $grade
 * @property-read Collection|Student[] $students
 * @property-read Collection|EducatorClass[] $educatorClass
 * @property-read Collection|Educator[] $educators
 * @property int $department_id 对应的部门ID
 * @property-read Department $department
 * @method static Builder|Squad whereDepartmentId($value)
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department() { return $this->belongsTo('App\Models\Department'); }
    
    /**
     * 返回指定班级所属的年级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade() { return $this->belongsTo('App\Models\Grade'); }
    
    /**
     * 获取指定班级包含的所有学生对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students() { return $this->hasMany('App\Models\Student','class_id'); }
    
    /**
     * 获取指定班级包含的所有教职员工对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function educators() { return $this->belongsToMany('App\Models\Educator', 'educators_classes'); }
    
    /**
     * 保存班级
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    public function store(array $data, $fireEvent = false) {
        $class = $this->create($data);
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
    public function modify(array $data, $id, $fireEvent = false) {
        $class = $this->find($id);
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
     */
    public function remove($id, $fireEvent = false) {
        $class = $this->find($id);
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
    
    public function datatable() {
        $columns = [
            ['db' => 'Squad.id', 'dt' => 0],
            [
                'db' => 'Squad.name', 'dt' => 1,
                'formatter' => function($d) {
                    return '<i class="fa fa-users"></i>&nbsp;' . $d;
                }
            ],
            [
                'db' => 'Grade.name as gradename', 'dt' => 2,
                'formatter' => function($d) {
                    return '<i class="fa fa-object-group"></i>&nbsp;' . $d;
                }
            ],
            [
                'db' => 'School.name as schoolname', 'dt' => 3,
                'formatter' => function($d) {
                    return '<i class="fa fa-university"></i>&nbsp;' . $d;
                }
            ],
            [
                'db' => 'Squad.educator_ids', 'dt' => 4,
                'formatter' => function ($d) {
                    if (empty($d)) { return ''; }
                    $educatorIds = explode(',', $d);
                    $educators = [];
                    foreach ($educatorIds as $id) {
                        $educator = Educator::whereId($id)->first();
                        if ($educator) {
                            if ($educator->user) {
                                $educators[] = $educator->user->realname;
                            }
                        }
                        
                    }
                    return implode(', ',$educators);
                },
            ],
            ['db' => 'Squad.created_at', 'dt' => 5],
            ['db' => 'Squad.updated_at', 'dt' => 6],
            [
                'db'        => 'Squad.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
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
        $school = new School();
        $schoolId = $school->getSchoolId();
        $condition = 'Grade.school_id = ' . $schoolId;
        return Datatable::simple($this, $columns, $joins, $condition);
        
    }
    
}
