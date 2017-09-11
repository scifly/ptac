<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\EducatorAttendanceSettingRequest;

/**
 * App\Models\EducatorAttendanceSetting
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EducatorAttendance[] $educatorAttendance
 * @property-read \App\Models\School $schools
 */
class EducatorAttendanceSetting extends Model {
    //
    protected $table = 'educator_attendance_settings';
    protected $fillable = [
        'name',
        'school_id',
        'start',
        'end',
        'inorout'
    ];
    
    /**
     * 教职工考勤设置与教职工考勤
     */
    public function educatorAttendance() {
        return $this->hasMany('App\Models\EducatorAttendance', 'eas_id');
    }
    
    /**
     * 教职工考勤设置与学校
     */
    public function schools() {
        return $this->belongsTo('App\Models\School');
    }

    public function datatable() {

        $columns = [
            ['db' => 'EducatorAttendanceSetting.id', 'dt' => 0],
            ['db' => 'EducatorAttendanceSetting.name', 'dt' => 1],
            ['db' => 'School.name as schoolname ', 'dt' => 2],
            ['db' => 'EducatorAttendanceSetting.start', 'dt' => 3],
            ['db' => 'EducatorAttendanceSetting.end', 'dt' => 4],
            ['db' => 'EducatorAttendanceSetting.inorout', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d == 1 ? '进' : '出';
                }
            ],
            ['db' => 'EducatorAttendanceSetting.created_at', 'dt' => 6],
            [
                'db' => 'EducatorAttendanceSetting.updated_at', 'dt' => 7,
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

        return Datatable::simple($this, $columns,$joins);

    }
}
