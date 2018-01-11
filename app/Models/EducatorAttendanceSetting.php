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
 * App\Models\EducatorAttendanceSetting 教职员工考勤设置
 *
 * @property int $id
 * @property string $name 考勤设置名称
 * @property int $school_id 考勤设置所属学校ID
 * @property string $start 考勤设置起始时间
 * @property string $end 考勤设置结束时间
 * @property int $inorout 进或出
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|EducatorAttendance[] $educatorAttendances
 * @property-read School $school
 * @method static Builder|EducatorAttendanceSetting whereCreatedAt($value)
 * @method static Builder|EducatorAttendanceSetting whereEnabled($value)
 * @method static Builder|EducatorAttendanceSetting whereEnd($value)
 * @method static Builder|EducatorAttendanceSetting whereId($value)
 * @method static Builder|EducatorAttendanceSetting whereInorout($value)
 * @method static Builder|EducatorAttendanceSetting whereName($value)
 * @method static Builder|EducatorAttendanceSetting whereSchoolId($value)
 * @method static Builder|EducatorAttendanceSetting whereStart($value)
 * @method static Builder|EducatorAttendanceSetting whereUpdatedAt($value)
 * @mixin Eloquent
 */
class EducatorAttendanceSetting extends Model {

    protected $table = 'educator_attendance_settings';

    protected $fillable = [
        'name', 'school_id', 'start',
        'end', 'inorout', 'enabled'
    ];

    /**
     * 获取对应的所有教职员工考勤记录对象
     *
     * @return HasMany
     */
    public function educatorAttendances() { return $this->hasMany('App\Models\EducatorAttendance', 'eas_id'); }

    /**
     * 返回所属的学校对象
     *
     * @return BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }

    /**
     * 教职员工考勤设置列表
     *
     * @return array
     */
    static function datatable() {
        
        $columns = [
            ['db' => 'EducatorAttendanceSetting.id', 'dt' => 0],
            ['db' => 'EducatorAttendanceSetting.name', 'dt' => 1],
            [
                'db' => 'School.name as schoolname ', 'dt' => 2,
                'formatter' => function ($d) {
                    return '<i class="fa fa-university"></i>&nbsp;' . $d;
                }
            ],
            ['db' => 'EducatorAttendanceSetting.start', 'dt' => 3],
            ['db' => 'EducatorAttendanceSetting.end', 'dt' => 4],
            [
                'db' => 'EducatorAttendanceSetting.inorout', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? '进' : '出';
                },
            ],
            ['db' => 'EducatorAttendanceSetting.created_at', 'dt' => 6],
            [
                'db' => 'EducatorAttendanceSetting.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = EducatorAttendanceSetting.school_id',
                ],
            ],
        ];
        $condition = 'EducatorAttendanceSetting.school_id = ' . School::schoolId();
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);

    }

}
