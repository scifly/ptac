<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\StudentAttendanceSetting
 *
 * @property int $id
 * @property string $name 学生考勤设置名称
 * @property int $grade_id 所属年级ID
 * @property int $semester_id 学期ID
 * @property int $ispublic 是否为学校公用考勤设置
 * @property string $start 考勤时段起始时间
 * @property string $end 考勤时段截止时间
 * @property string $day 星期几？
 * @property int $inorout 进或出
 * @property string $msg_template 考勤消息模板
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|StudentAttendanceSetting whereCreatedAt($value)
 * @method static Builder|StudentAttendanceSetting whereDay($value)
 * @method static Builder|StudentAttendanceSetting whereEnd($value)
 * @method static Builder|StudentAttendanceSetting whereGradeId($value)
 * @method static Builder|StudentAttendanceSetting whereId($value)
 * @method static Builder|StudentAttendanceSetting whereInorout($value)
 * @method static Builder|StudentAttendanceSetting whereIspublic($value)
 * @method static Builder|StudentAttendanceSetting whereMsgTemplate($value)
 * @method static Builder|StudentAttendanceSetting whereName($value)
 * @method static Builder|StudentAttendanceSetting whereSemesterId($value)
 * @method static Builder|StudentAttendanceSetting whereStart($value)
 * @method static Builder|StudentAttendanceSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Grade $grade
 * @property-read Semester $semester
 */
class StudentAttendanceSetting extends Model {

    //
    protected $table = 'student_attendance_settings';
    protected $fillable = [
        'name', 'grade_id', 'semester_id',
        'ispublic', 'start', 'end',
        'day', 'inorout', 'msg_template',
    ];
    
    /**
     * 返回指定学生考勤设置所属的年级对象
     *
     * @return BelongsTo
     */
    public function grade() { return $this->belongsTo('App\Models\Grade'); }
    
    /**
     * 返回指定学生考勤设置所属的学期对象
     *
     * @return BelongsTo
     */
    public function semester() {
        
        return $this->belongsTo('App\Models\Semester', 'semester_id', 'id');
        
    }

    public function datatable() {
        
        $columns = [
            ['db' => 'StudentAttendanceSetting.id', 'dt' => 0],
            ['db' => 'StudentAttendanceSetting.name', 'dt' => 1],
            ['db' => 'Grade.name as gradename', 'dt' => 2],
            ['db' => 'Semester.name as semestername', 'dt' => 3],
            [
                'db' => 'StudentAttendanceSetting.ispublic', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d == 1 ? '是' : '否';
                },
            ],
            ['db' => 'StudentAttendanceSetting.start', 'dt' => 5],
            ['db' => 'StudentAttendanceSetting.end', 'dt' => 6],
            ['db' => 'StudentAttendanceSetting.day', 'dt' => 7],
            ['db' => 'StudentAttendanceSetting.inorout', 'dt' => 8,
                'formatter' => function ($d) {
                    return $d == 1 ? '进' : '出';
                },
            ],
            ['db' => 'StudentAttendanceSetting.msg_template', 'dt' => 9],
            [
                'db' => 'StudentAttendanceSetting.updated_at', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'grades',
                'alias' => 'Grade',
                'type' => 'INNER',
                'conditions' => [
                    'Grade.id = StudentAttendanceSetting.grade_id',
                ],
            ],
            [
                'table' => 'semesters',
                'alias' => 'Semester',
                'type' => 'INNER',
                'conditions' => [
                    'Semester.id = StudentAttendanceSetting.semester_id',
                ],
            ],
        ];
        $school = new School();
        $condition = 'Semester.school_id = ' . $school->getSchoolId();
        unset($school);
        
        return Datatable::simple($this, $columns, $joins,$condition);

    }
}
