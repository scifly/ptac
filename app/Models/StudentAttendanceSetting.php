<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\StudentAttendanceSetting 学生考勤设置
 *
 * @property int $id
 * @property string $name 学生考勤设置名称
 * @property int $grade_id 所属年级ID
 * @property int $semester_id 学期ID
 * @property int $ispublic 是否为学校公用考勤设置
 * @property string $start 考勤时段起始时间
 * @property string $end 考勤时段截止时间
 * @property string $day 星期几？
 * @property int $direction 进或出
 * @property string $msg_template 考勤消息模板
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Grade $grade
 * @property-read Semester $semester
 * @property-read StudentAttendance $studentAttendance
 * @property-read Collection|StudentAttendance[] $studentAttendances
 * @method static Builder|StudentAttendanceSetting whereCreatedAt($value)
 * @method static Builder|StudentAttendanceSetting whereDay($value)
 * @method static Builder|StudentAttendanceSetting whereEnd($value)
 * @method static Builder|StudentAttendanceSetting whereGradeId($value)
 * @method static Builder|StudentAttendanceSetting whereId($value)
 * @method static Builder|StudentAttendanceSetting whereDirection($value)
 * @method static Builder|StudentAttendanceSetting whereIspublic($value)
 * @method static Builder|StudentAttendanceSetting whereMsgTemplate($value)
 * @method static Builder|StudentAttendanceSetting whereName($value)
 * @method static Builder|StudentAttendanceSetting whereSemesterId($value)
 * @method static Builder|StudentAttendanceSetting whereStart($value)
 * @method static Builder|StudentAttendanceSetting whereUpdatedAt($value)
 * @method static Builder|StudentAttendanceSetting newModelQuery()
 * @method static Builder|StudentAttendanceSetting newQuery()
 * @method static Builder|StudentAttendanceSetting query()
 * @mixin Eloquent
 */
class StudentAttendanceSetting extends Model {
    
    use ModelTrait;
    
    protected $table = 'student_attendance_settings';
    
    protected $fillable = [
        'name', 'grade_id', 'semester_id',
        'ispublic', 'start', 'end',
        'day', 'direction', 'msg_template',
    ];
    
    /**
     * 返回指定学生考勤设置所属的年级对象
     *
     * @return BelongsTo
     */
    function grade() { return $this->belongsTo('App\Models\Grade'); }
    
    /**
     * 返回指定学生考勤设置所属的学期对象
     *
     * @return BelongsTo
     */
    function semester() {
        
        return $this->belongsTo('App\Models\Semester', 'semester_id', 'id');
        
    }
    
    /**
     * 返回指定学生考勤设置对应的所有学生考勤记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function studentAttendances() {
        
        return $this->hasMany('App\Models\StudentAttendance', 'id', 'sas_id');
        
    }
    
    /**
     * 学生考勤设置记录列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'StudentAttendanceSetting.id', 'dt' => 0],
            ['db' => 'StudentAttendanceSetting.name', 'dt' => 1],
            [
                'db' => 'Grade.name as gradename', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::grade($d);
                }
            ],
            ['db' => 'Semester.name as semestername', 'dt' => 3],
            [
                'db'        => 'StudentAttendanceSetting.ispublic', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d == 1 ? '是' : '否';
                },
            ],
            ['db' => 'StudentAttendanceSetting.start', 'dt' => 5],
            ['db' => 'StudentAttendanceSetting.end', 'dt' => 6],
            ['db' => 'StudentAttendanceSetting.day', 'dt' => 7],
            ['db'        => 'StudentAttendanceSetting.direction', 'dt' => 8,
             'formatter' => function ($d) {
                 return $d
                     ? sprintf(Snippet::BADGE_GREEN, '进')
                     : sprintf(Snippet::BADGE_RED, '出');
             },
            ],
            ['db' => 'StudentAttendanceSetting.msg_template', 'dt' => 9],
            [
                'db'        => 'StudentAttendanceSetting.updated_at', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'grades',
                'alias'      => 'Grade',
                'type'       => 'INNER',
                'conditions' => [
                    'Grade.id = StudentAttendanceSetting.grade_id',
                ],
            ],
            [
                'table'      => 'semesters',
                'alias'      => 'Semester',
                'type'       => 'INNER',
                'conditions' => [
                    'Semester.id = StudentAttendanceSetting.semester_id',
                ],
            ],
        ];
        $condition = 'Semester.school_id = ' . $this->schoolId();
        $user = Auth::user();
        if (!in_array($user->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND StudentAttendanceSetting.grade_id IN (' . implode(',', $this->gradeIds()) . ')';
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存学生考勤设置
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新学生考勤设置
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 移除学生考勤设置
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $this->purge(
                    [class_basename($this), 'StudentAttendance'],
                    'sas_id', 'purge', $id
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}
