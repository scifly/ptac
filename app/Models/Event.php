<?php
namespace App\Models;

use App\Helpers\{ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
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
 * @property-read Room|null $room
 */
class Event extends Model {
    
    use ModelTrait;
    
    protected $table = 'events';
    protected $fillable = [
        'start', 'end', 'room_id', 'enabled',
    ];
    
    /** @return BelongsTo */
    function room() { return $this->belongsTo('App\Models\Room'); }
    
    /**
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                throw_if(
                    $event = $this->find($id),
                    new Exception(__('messages.not_found'))
                );
                $event->update($data);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除事件
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id) {
    
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                Request::replace(['ids' => $ids]);
                $this->purge(['Event', 'Message'], 'event_id', 'reset');
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}