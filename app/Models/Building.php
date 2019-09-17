<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasManyThrough};
use Illuminate\Support\{Carbon, Facades\DB, Facades\Request};
use Throwable;

/**
 * Class Building
 *
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 名称
 * @property int $floors 楼层数
 * @property string|null $remark 备注（地址）
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 状态
 * @property-read Collection|Bed[] $beds
 * @property-read int|null $beds_count
 * @property-read Collection|Room[] $rooms
 * @property-read int|null $rooms_count
 * @property-read School $school
 * @method static Builder|Building newModelQuery()
 * @method static Builder|Building newQuery()
 * @method static Builder|Building query()
 * @method static Builder|Building whereCreatedAt($value)
 * @method static Builder|Building whereEnabled($value)
 * @method static Builder|Building whereFloors($value)
 * @method static Builder|Building whereId($value)
 * @method static Builder|Building whereName($value)
 * @method static Builder|Building whereRemark($value)
 * @method static Builder|Building whereSchoolId($value)
 * @method static Builder|Building whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Building extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'floors', 'remark', 'enabled'];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
    function rooms() { return $this->hasMany('App\Models\Room'); }
    
    /** @return HasManyThrough */
    function beds() { return $this->hasManyThrough('App\Models\Bed', 'App\Models\Room'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /** @return array */
    function index() {
        
        $columns = [
            ['db' => 'Building.id', 'dt' => 0],
            ['db' => 'Building.name', 'dt' => 1],
            ['db' => 'Building.floors', 'dt' => 2],
            ['db' => 'Building.remark', 'dt' => 3],
            ['db' => 'Building.created_at', 'dt' => 4],
            ['db' => 'Building.updated_at', 'dt' => 5],
            [
                'db'        => 'Building.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Building.school_id'
                ]
            ]
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
        
        try {
            DB::transaction(function () use ($data, $id) {
                throw_if(
                    !$building = $this->find($id),
                    new Exception(__('messages.not_found'))
                );
                $building->update($data);
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
                $this->purge(['Building', 'Room'], 'building_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}
