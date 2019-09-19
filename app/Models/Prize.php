<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * Class Prize - 奖励/处罚
 *
 * @package App\Models
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 名称
 * @property int $score 分数
 * @property string|null $remark 备注（奖惩内容）
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read School $school
 * @method static Builder|Prize newModelQuery()
 * @method static Builder|Prize newQuery()
 * @method static Builder|Prize query()
 * @method static Builder|Prize whereCreatedAt($value)
 * @method static Builder|Prize whereEnabled($value)
 * @method static Builder|Prize whereId($value)
 * @method static Builder|Prize whereName($value)
 * @method static Builder|Prize whereRemark($value)
 * @method static Builder|Prize whereSchoolId($value)
 * @method static Builder|Prize whereScore($value)
 * @method static Builder|Prize whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Prize extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'score', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return array */
    function index() {
        
        $columns = [
            ['db' => 'Prize.id', 'dt' => 0],
            ['db' => 'Prize.name', 'dt' => 1],
            [
                'db' => 'Prize.score', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d ? '+' : '-';
                }
            ],
            ['db' => 'Prize.remark', 'dt' => 3],
            ['db' => 'Prize.created_at', 'dt' => 4],
            ['db' => 'Prize.updated_at', 'dt' => 5],
            [
                'db' => 'Prize.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                }
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Prize.school_id'
                ]
            ]
        ];
        $condition = 'Prize.school_id = ' . $this->schoolId();
        
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
                    !$prize = $this->find($id),
                    new Exception(__('messages.not_found'))
                );
                $prize->update($data);
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
                $this->purge(['Prize'], 'indicator_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
