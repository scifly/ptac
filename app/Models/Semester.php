<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ReflectionException;

/**
 * App\Models\Semester 学期
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $name 学期名称
 * @property string|null $remark 备注
 * @property string $start_date 学期开始日期
 * @property string $end_date 学期截止日期
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read School $school
 * @property-read Collection|StudentAttendanceSetting[] $studentAttendanceSettings
 * @method static Builder|Semester whereCreatedAt($value)
 * @method static Builder|Semester whereEnabled($value)
 * @method static Builder|Semester whereEndDate($value)
 * @method static Builder|Semester whereId($value)
 * @method static Builder|Semester whereName($value)
 * @method static Builder|Semester whereRemark($value)
 * @method static Builder|Semester whereSchoolId($value)
 * @method static Builder|Semester whereStartDate($value)
 * @method static Builder|Semester whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Semester extends Model {
    
    use ModelTrait;

    protected $fillable = [
        'school_id', 'name', 'remark',
        'start_date', 'end_date', 'enabled',
    ];
    
    /**
     * 返回学期记录所属的学校对象
     * 
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 返回学期记录包含的所有学生考勤设置对象
     * 
     * @return HasMany
     */
    function studentAttendanceSettings() { 
        
        return $this->hasMany('App\Models\StudentAttendanceSetting'); 
        
    }
    
    /**
     * 创建学期
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return self::create($data) ? true : false;
        
    }
    
    /**
     * 更新学期
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $semester = $this->find($id);
        if (!$semester) { return false; }
        
        return $this->update($data);
        
    }
    
    /**
     * 删除学期
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     * @throws ReflectionException
     */
    function remove($id) {
        
        $semester = self::find($id);
        if (!$semester) { return false; }
        
        return $this->removable($semester) ? $semester->delete() : false;
        
    }
    
    /**
     * 学期列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Semester.id', 'dt' => 0],
            ['db' => 'Semester.name as semestername', 'dt' => 1],
            ['db' => 'Semester.start_date', 'dt' => 2],
            ['db' => 'Semester.end_date', 'dt' => 3],
            ['db' => 'Semester.created_at', 'dt' => 4],
            ['db' => 'Semester.updated_at', 'dt' => 5],
            [
                'db' => 'Semester.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $condition = 'Semester.school_id = ' . $this->schoolId();
    
        return Datatable::simple(
            $this->getModel(), $columns, null, $condition
        );

    }

}