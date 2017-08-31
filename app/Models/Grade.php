<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\GradeRequest;
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
 */
class Grade extends Model {
    
    protected $fillable = [
        'name', 'school_id',
        'educator_ids', 'enabled',
    ];
    
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
     * 判断年级记录是否存在
     *
     * @param GradeRequest $request
     * @param null $id
     * @return bool
     */
    public function existed(GradeRequest $request, $id = NULL) {
        
        if (!$id) {
            $grade = $this->where('school_id', $request->input('school_id'))
                ->where('name', $request->input('name'))->first();
        } else {
            $grade = $this->where('school_id', $request->input('school_id'))
                ->where('id', '<>', $id)
                ->where('name', $request->input('name'))->first();
        }
        return $grade ? true : false;
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Grade.id', 'dt' => 0],
            ['db' => 'Grade.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'Grade.educator_ids', 'dt' => 3],
            ['db' => 'Grade.created_at', 'dt' => 4],
            ['db' => 'Grade.updated_at', 'dt' => 5],
            
            [
                'db' => 'Grade.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Grade.school_id'
                ]
            
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }

}
