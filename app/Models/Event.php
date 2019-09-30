<?php
namespace App\Models;

use App\Helpers\{ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Throwable;

/**
 * App\Models\Event 事件(日程)
 *
 * @property int $id
 * @property string $start 事件开始时间
 * @property string $end 事件结束时间
 * @property string $room_id 房间id（事件发生地点）
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Educator $educator
 * @property-read Subject $subject
 * @property-read User $user
 * @property-read Message $message
 * @property-read Room|null $room
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereRoomId($value)
 * @method static Builder|Event whereEnabled($value)
 * @method static Builder|Event whereEnd($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereStart($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @mixin Eloquent
 */
class Event extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['start', 'end', 'room_id', 'enabled'];
    
    /** @return BelongsTo */
    function room() { return $this->belongsTo('App\Models\Room'); }
    
    /**
     * 保存事件
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新事件
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除事件
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id) {
    
        return $this->purge($id, [
            'reset.event_id' => ['Message']
        ]);
        
    }
    
}