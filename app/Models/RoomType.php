<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{DB, Request};
use Throwable;

/**
 * Class RoomType
 *
 * @package App\Models
 * @property int $id
 * @property int $corp_id
 * @property int $room_function_id 房间功能：住宿、教学、会议等
 * @property string $name 名称
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 状态
 * @property-read RoomFunction $roomFunction
 * @property-read Corp $corp
 * @method static Builder|RoomType newModelQuery()
 * @method static Builder|RoomType newQuery()
 * @method static Builder|RoomType query()
 * @method static Builder|RoomType whereCreatedAt($value)
 * @method static Builder|RoomType whereEnabled($value)
 * @method static Builder|RoomType whereId($value)
 * @method static Builder|RoomType whereCorpId($value)
 * @method static Builder|RoomType whereName($value)
 * @method static Builder|RoomType whereRemark($value)
 * @method static Builder|RoomType whereRoomFunctionId($value)
 * @method static Builder|RoomType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class RoomType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['room_function_id', 'name', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function roomFunction() { return $this->belongsTo('App\Models\RoomFunction'); }
    
    /** @return BelongsTo */
    function corp() { return $this->belongsTo('App\Models\Corp'); }
    
    /** @return array */
    function index() {
        
        $columns = [
            ['db' => 'RoomType.id', 'dt' => 0],
            ['db' => 'RoomType.name', 'dt' => 1],
            ['db' => 'RoomFunction.name as rfname', 'dt' => 2],
            ['db' => 'RoomType.remark', 'dt' => 3],
            ['db' => 'RoomType.created_at', 'dt' => 4],
            ['db' => 'RoomType.updated_at', 'dt' => 5],
            [
                'db'        => 'Building.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'room_functions',
                'alias' => 'RoomFunction',
                'type' => 'INNER',
                'conditions' => [
                    'RoomFunction.id = RoomType.room_function_id'
                ]
            ]
        ];
        $condition = 'RoomType.corp_id = ' . $this->corpId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
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
                   !$rt = $this->find($id),
                   new Exception(__('messages.not_found'))
               );
               $rt->update($data);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id) {
    
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                Request::replace(['ids' => $ids]);
                $this->purge(['RoomType', 'Room'], 'room_type_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
    /** @return array */
    function compose() {
    
        return explode('/', Request::path())[1] == 'index'
            ? ['titles' => ['#', '名称', '房间功能', '备注', '创建于', '更新于', '状态 . 操作']]
            : ['rfs' => RoomFunction::pluck('name', 'id')];
            
    }
    
}
