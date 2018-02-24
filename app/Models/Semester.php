<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $fillable = [
        'school_id', 'name', 'remark',
        'start_date', 'end_date', 'enabled',
    ];
    
    /**
     * 返回学期记录所属的学校对象
     * 
     * @return BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 返回学期记录包含的所有学生考勤设置对象
     * 
     * @return HasMany
     */
    public function studentAttendanceSettings() { return $this->hasMany('App\Models\StudentAttendanceSetting'); }
    
    /**
     * 学期列表
     *
     * @return array
     */
    public function datatable() {
        
        $columns = [
            ['db' => 'Semester.id', 'dt' => 0],
            ['db' => 'Semester.name as semestername', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'Semester.start_date', 'dt' => 3],
            ['db' => 'Semester.end_date', 'dt' => 4],
            ['db' => 'Semester.created_at', 'dt' => 5],
            ['db' => 'Semester.updated_at', 'dt' => 6],
            [
                'db' => 'Semester.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Semester.school_id',
                ],
            ],
        ];
        $condition = 'Semester.school_id = ' . School::schoolId();
    
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);

    }

}
