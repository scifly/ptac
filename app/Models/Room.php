<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as ECollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * Class Room
 *
 * @package App\Models
 * @property int $id
 * @property int $building_id 所属大楼id
 * @property int $room_type_id 房间类型id
 * @property string $name 名称
 * @property int $floor 所处楼层
 * @property int|null $volume 容量（可容纳人数）
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 状态
 * @property-read Building $building
 * @property-read RoomType $roomType
 * @method static Builder|Room newModelQuery()
 * @method static Builder|Room newQuery()
 * @method static Builder|Room query()
 * @method static Builder|Room whereBuildingId($value)
 * @method static Builder|Room whereCreatedAt($value)
 * @method static Builder|Room whereEnabled($value)
 * @method static Builder|Room whereFloor($value)
 * @method static Builder|Room whereId($value)
 * @method static Builder|Room whereName($value)
 * @method static Builder|Room whereRemark($value)
 * @method static Builder|Room whereRoomTypeId($value)
 * @method static Builder|Room whereUpdatedAt($value)
 * @method static Builder|Room whereVolume($value)
 * @mixin Eloquent
 * @property-read ECollection|Bed[] $beds
 * @property-read int|null $beds_count
 */
class Room extends Model {

    use ModelTrait;
    
    protected $fillable = [
        'building_id', 'room_type_id', 'name',
        'floor', 'volume', 'remark', 'enabled'
    ];
    
    /** @return BelongsTo */
    function building() { return $this->belongsTo('App\Models\Building'); }
    
    /** @return BelongsTo */
    function roomType() { return $this->belongsTo('App\Models\RoomType'); }
    
    /** @return HasMany */
    function beds() { return $this->hasMany('App\Models\Bed'); }
    
    /** @return array */
    function index() {
        
        $columns = [
            ['db' => 'Room.id', 'dt' => 0],
            ['db' => 'Room.name', 'dt' => 1],
            ['db' => 'Building.name as bname', 'dt' => 2],
            ['db' => 'RoomType.name as rname', 'dt' => 3],
            ['db' => 'Room.floor', 'dt' => 4],
            ['db' => 'Room.volume', 'dt' => 5],
            ['db' => 'Room.created_at', 'dt' => 6],
            ['db' => 'Room.updated_at', 'dt' => 7],
            [
                'db'        => 'Room.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'buildings',
                'alias' => 'Building',
                'type' => 'INNER',
                'conditions' => [
                    'Building.id = Room.building_id'
                ]
            ],
            [
                'table' => 'room_types',
                'alias' => 'RoomType',
                'type' => 'INNER',
                'conditions' => [
                    'RoomType.id = Room.room_type_id'
                ]
            ],
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Building.school_id'
                ]
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        
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
    
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id) {
    
        return $this->purge($id, [
            'purge.room_id' => ['Bed']
        ]);
    
    }
    
    /** @return array */
    function compose() {
        
        $nil = collect([0 => '全部']);
        $rts = RoomType::whereCorpId($this->corpId())->pluck('name', 'id');
        $buildings = Building::whereSchoolId($this->schoolId())->pluck('name', 'id');
        
        return explode('/', Request::path())[1] == 'index'
            ? [
                'titles' => [
                    '#', '名称','所属楼舍',
                    [
                        'title' => '房间类型',
                        'html'  => $this->htmlSelect($nil->union($rts), 'filter_building')
                    ],
                    '楼层', '创建于', '更新于', '状态 . 操作'
                ],
                'filter' => true,
                'batch' => true
            ]
            : [
                'rts' => $rts,
                'buildings' => $buildings
            ];
        
    }
    
    /**
     * @param $type
     * @return Collection
     */
    function rooms($type) {
        
        return School::find($this->schoolId())->rooms->filter(
            function (Room $room) use ($type) {
                return $room->roomType->roomFunction->name == $type;
            }
        )->pluck('name', 'id');
        
    }
    
}
