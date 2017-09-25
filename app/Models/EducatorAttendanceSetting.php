<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducatorAttendanceSetting 教职员工考勤设置
 *
 * @property int $id
 * @property string $name 考勤设置名称
 * @property int $school_id 考勤设置所属学校ID
 * @property string $start 考勤设置起始时间
 * @property string $end 考勤设置结束时间
 * @property int $inorout 进或出
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|EducatorAttendanceSetting whereCreatedAt($value)
 * @method static Builder|EducatorAttendanceSetting whereEnd($value)
 * @method static Builder|EducatorAttendanceSetting whereId($value)
 * @method static Builder|EducatorAttendanceSetting whereInorout($value)
 * @method static Builder|EducatorAttendanceSetting whereName($value)
 * @method static Builder|EducatorAttendanceSetting whereSchoolId($value)
 * @method static Builder|EducatorAttendanceSetting whereStart($value)
 * @method static Builder|EducatorAttendanceSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read EducatorAttendance[] $educatorAttendance
 * @property-read School $schools
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EducatorAttendance[] $educatorAttendances
 * @property-read \App\Models\School $school
 */
class EducatorAttendanceSetting extends Model {
    
    protected $table = 'educator_attendance_settings';
    
    protected $fillable = [
        'name', 'school_id', 'start',
        'end', 'inorout'
    ];
    
    /**
     * 获取对应的所有教职员工考勤记录对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function educatorAttendances() { return $this->hasMany('App\Models\EducatorAttendance', 'eas_id'); }
    
    /**
     * 返回所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'EducatorAttendanceSetting.id', 'dt' => 0],
            ['db' => 'EducatorAttendanceSetting.name', 'dt' => 1],
            ['db' => 'School.name as schoolname ', 'dt' => 2],
            ['db' => 'EducatorAttendanceSetting.start', 'dt' => 3],
            ['db' => 'EducatorAttendanceSetting.end', 'dt' => 4],
            [
                'db' => 'EducatorAttendanceSetting.inorout', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? '进' : '出';
                }
            ],
            ['db' => 'EducatorAttendanceSetting.created_at', 'dt' => 6],
            ['db' => 'EducatorAttendanceSetting.updated_at', 'dt' => 7],
            ['db' => 'EducatorAttendanceSetting.updated_at', 'dt' => 7],
            [
                'db' => 'EducatorAttendanceSetting.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = EducatorAttendanceSetting.school_id'
                ]
            ],
        ];
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}
