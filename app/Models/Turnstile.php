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
 * App\Models\Turnstile 门禁设备
 *
 * @property int $id
 * @property string $sn 门禁设备名称
 * @property string $doors 门数
 * @property string $ip ip地址
 * @property string $port 端口号
 * @property string $location 门禁设备安装地点
 * @property int $school_id 所属学校ID
 * @property string $deviceid 门禁设备id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 门禁状态
 * @property-read School $school
 * @property-read StudentAttendance[] $studentAttendances
 * @method static Builder|Turnstile whereCreatedAt($value)
 * @method static Builder|Turnstile whereEnabled($value)
 * @method static Builder|Turnstile whereId($value)
 * @method static Builder|Turnstile whereIp($value)
 * @method static Builder|Turnstile wherePort($value)
 * @method static Builder|Turnstile whereLocation($value)
 * @method static Builder|Turnstile whereDeviceid($value)
 * @method static Builder|Turnstile whereSn($value)
 * @method static Builder|Turnstile whereSchoolId($value)
 * @method static Builder|Turnstile whereUpdatedAt($value)
 * @method static Builder|Turnstile newModelQuery()
 * @method static Builder|Turnstile newQuery()
 * @method static Builder|Turnstile query()
 * @method static Builder|Turnstile whereDoors($value)
 * @mixin Eloquent
 */
class Turnstile extends Model {
    
    use ModelTrait;
    
    protected $table = 'turnstiles';
    
    protected $fillable = [
        'name', 'location', 'school_id',
        'machineid', 'enabled',
    ];
    
    /**
     * 返回门禁设备所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定门禁设备的学生考勤记录对象
     *
     * @return HasMany
     */
    function studentAttendances() { return $this->hasMany('App\Models\StudentAttendance'); }
    
    /**
     * 门禁设备列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Turnstile.id', 'dt' => 0],
            ['db' => 'Turnstile.sn', 'dt' => 1],
            ['db' => 'Turnstile.location', 'dt' => 2],
            ['db' => 'Turnstile.doors', 'dt' => 3],
            ['db' => 'Turnstile.deviceid', 'dt' => 4],
            ['db' => 'Turnstile.created_at', 'dt' => 5],
            ['db' => 'Turnstile.updated_at', 'dt' => 6],
            [
                'db'        => 'Turnstile.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false, true, false);
                },
            ],
        ];
        $condition = 'Turnstile.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, null, $condition
        );
        
    }
    
    /**
     * 更新门禁设备
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
     * 移除门禁设备
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
                    'turnstile_id', 'purge', $id
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}