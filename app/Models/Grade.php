<?php
namespace App\Models;

use App\Events\GradeCreated;
use App\Events\GradeDeleted;
use App\Events\GradeUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Grade 年级
 *
 * @property int $id
 * @property string $name 年级名称
 * @property int $school_id 所属学校ID
 * @property string $educator_ids 年级主任教职员工ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Grade whereCreatedAt($value)
 * @method static Builder|Grade whereEducatorIds($value)
 * @method static Builder|Grade whereEnabled($value)
 * @method static Builder|Grade whereId($value)
 * @method static Builder|Grade whereName($value)
 * @method static Builder|Grade whereSchoolId($value)
 * @method static Builder|Grade whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\School $school
 * @property-read Collection|Squad[] $squads
 * @property-read Collection|Subject[] $subject
 * @property-read Collection|Squad[] $classes
 * @property-read Collection|Student[] $students
 * @property int $department_id 对应的部门ID
 * @property-read Department $department
 * @property-read StudentAttendanceSetting $studentAttendanceSetting
 * @method static Builder|Grade whereDepartmentId($value)
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department() { return $this->belongsTo('App\Models\Department'); }
    
    /**
     * 返回指定年级所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定年级包含的所有班级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classes() { return $this->hasMany('App\Models\Squad'); }
    
    /**
     * 获取指定年级包含的学生考勤设置对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentAttendanceSetting() { return $this->hasOne('App\Models\StudentAttendanceSetting'); }
    
    /**
     * 通过Squad中间对象获取指定年级包含的所有学生对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function students() { return $this->hasManyThrough('App\Models\Student', 'App\Models\Squad'); }
    
    /**
     * 根据学校ID返回年级列表(id, name)
     *
     * @param array $schoolIds
     * @return array|\Illuminate\Support\Collection
     */
    public function grades(array $schoolIds = []) {
        
        if (sizeof($schoolIds) === 1) {
            return $this->where('school_id', $schoolIds[0])
                ->where('enabled', 1)
                ->get()->pluck('id', 'name');
        }
        # 获取学校列表
        $schools = School::whereEnabled(1)->get()->pluck('id', 'name');
        # 获取所有年级对象
        if (empty($schoolIds)) {
            $grades = $this->whereEnabled(1)->get(['id', 'name', 'school_id']);
            
            return $this->getGradesList($grades, $schools);
        }
        # 获取指定学校的所有年级列表
        $grades = $this->whereIn('school_id', $schoolIds)
            ->where('enabled', 1)->get();
        
        return $this->getGradesList($grades, $schools);
        
    }
    
    private function getGradesList($grades, $schools) {
        
        $gradesList = [];
        foreach ($grades as $grade) {
            $gradesList[$schools[$grade['school_id']]][$grade['id']] = $grade['name'];
        }
        
        return $gradesList;
        
    }
    
    /**
     * 保存年级
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    public function store(array $data, $fireEvent = false) {
        
        $grade = $this->create($data);
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
    public function modify(array $data, $id, $fireEvent = false) {
        
        $grade = $this->find($id);
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
     */
    public function remove($id, $fireEvent = false) {
        
        $grade = $this->find($id);
        if (!$grade) {
            return false;
        }
        $removed = $this->removable($grade) ? $grade->delete() : false;
        if ($removed && $fireEvent) {
            event(new GradeDeleted($grade));
            
            return true;
        }
        
        return $removed ? true : false;
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Grade.id', 'dt' => 0],
            ['db' => 'Grade.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'Grade.educator_ids', 'dt' => 3,
             'formatter' => function ($d) {
                 $educatorId = explode(',',$d);
                 foreach ($educatorId as $id)
                 {
                     $educator[] = Educator::whereId($id)->first()->user->realname;
                 }
                 $userName = implode('&nbsp;,&nbsp;',$educator);
                 return $userName;
             },
            ],
            ['db' => 'Grade.created_at', 'dt' => 4],
            ['db' => 'Grade.updated_at', 'dt' => 5],
            [
                'db'        => 'Grade.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
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
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}
