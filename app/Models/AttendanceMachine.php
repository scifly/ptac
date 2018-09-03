<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\AttendanceMachine 考勤机
 *
 * @property int $id
 * @property string $name 考勤机名称
 * @property string $location 考勤机位置
 * @property int $school_id 所属学校ID
 * @property string $machineid 考勤机id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|AttendanceMachine whereCreatedAt($value)
 * @method static Builder|AttendanceMachine whereEnabled($value)
 * @method static Builder|AttendanceMachine whereId($value)
 * @method static Builder|AttendanceMachine whereLocation($value)
 * @method static Builder|AttendanceMachine whereMachineid($value)
 * @method static Builder|AttendanceMachine whereName($value)
 * @method static Builder|AttendanceMachine whereSchoolId($value)
 * @method static Builder|AttendanceMachine whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read School $school
 * @property-read StudentAttendance[] $studentAttendances
 */
class AttendanceMachine extends Model {
    
    use ModelTrait;
    
    protected $table = 'attendance_machines';
    
    protected $fillable = [
        'name', 'location', 'school_id',
        'machineid', 'enabled',
    ];
    
    /**
     * 返回考勤机所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定考勤机的学生考勤记录对象
     *
     * @return HasMany
     */
    function studentAttendances() { return $this->hasMany('App\Models\StudentAttendance'); }
    
    /**
     * 考勤机列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'AttendanceMachine.id', 'dt' => 0],
            ['db' => 'AttendanceMachine.name', 'dt' => 1],
            ['db' => 'AttendanceMachine.location', 'dt' => 2],
            ['db' => 'AttendanceMachine.machineid', 'dt' => 3],
            ['db' => 'AttendanceMachine.created_at', 'dt' => 4],
            ['db' => 'AttendanceMachine.updated_at', 'dt' => 5],
            [
                'db'        => 'AttendanceMachine.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'AttendanceMachine.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, null, $condition
        );
        
    }
    
    /**
     * 保存考勤机
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新考勤机
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Exception
     */
    function modify(array $data, $id = null) {
        
        return $id
            ? $this->find($id)->update($data)
            : $this->batch($this);
        
    }
    
    /**
     * 移除考勤机
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定考勤机的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                StudentAttendance::whereAttendanceMachineId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}