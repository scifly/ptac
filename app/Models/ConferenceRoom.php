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
 * App\Models\ConferenceRoom 会议室
 *
 * @property int $id
 * @property string $name 会议室名称
 * @property int $school_id 会议室所属学校ID
 * @property int $capacity 会议室容量
 * @property string $remark 会议室备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read School $schools
 * @property-read ConferenceQueue[] $conferenceQueues
 * @property-read School $school
 * @method static Builder|ConferenceRoom whereCapacity($value)
 * @method static Builder|ConferenceRoom whereCreatedAt($value)
 * @method static Builder|ConferenceRoom whereEnabled($value)
 * @method static Builder|ConferenceRoom whereId($value)
 * @method static Builder|ConferenceRoom whereName($value)
 * @method static Builder|ConferenceRoom whereRemark($value)
 * @method static Builder|ConferenceRoom whereSchoolId($value)
 * @method static Builder|ConferenceRoom whereUpdatedAt($value)
 * @method static Builder|ConferenceRoom newModelQuery()
 * @method static Builder|ConferenceRoom newQuery()
 * @method static Builder|ConferenceRoom query()
 * @mixin Eloquent
 */
class ConferenceRoom extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'school_id', 'capacity',
        'remark', 'enabled',
    ];
    
    /**
     * 返回会议室所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('\App\Models\School'); }
    
    /**
     * 获取指定会议室的会议队列
     *
     * @return HasMany
     */
    function conferenceQueues() { return $this->hasMany('App\Models\ConferenceQueue'); }
    
    /**
     * 会议室列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ConferenceRoom.id', 'dt' => 0],
            ['db' => 'ConferenceRoom.name', 'dt' => 1],
            ['db' => 'ConferenceRoom.capacity', 'dt' => 2],
            ['db' => 'ConferenceRoom.remark', 'dt' => 3],
            ['db' => 'ConferenceRoom.created_at', 'dt' => 4],
            ['db' => 'ConferenceRoom.updated_at', 'dt' => 5],
            [
                'db'        => 'ConferenceRoom.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'ConferenceRoom.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, null, $condition
        );
        
    }
    
    /**
     * 保存会议室
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新会议室
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
     * 删除会议室
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定会议室的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->delRelated('conference_room_id', 'ConferenceQueue', $id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
