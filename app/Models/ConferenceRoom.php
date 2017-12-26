<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ConferenceRoom 会议室
 *
 * @property int $id
 * @property string $name 会议室名称
 * @property int $school_id 会议室所属学校ID
 * @property int $capacity 会议室容量
 * @property string $remark 会议室备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ConferenceRoom whereCapacity($value)
 * @method static Builder|ConferenceRoom whereCreatedAt($value)
 * @method static Builder|ConferenceRoom whereEnabled($value)
 * @method static Builder|ConferenceRoom whereId($value)
 * @method static Builder|ConferenceRoom whereName($value)
 * @method static Builder|ConferenceRoom whereRemark($value)
 * @method static Builder|ConferenceRoom whereSchoolId($value)
 * @method static Builder|ConferenceRoom whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read School $schools
 * @property-read ConferenceQueue[] $conferenceQueues
 * @property-read School $school
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
    public function school() { return $this->belongsTo('\App\Models\School'); }

    /**
     * 获取指定会议室的会议队列
     *
     * @return HasMany
     */
    public function conferenceQueues() { return $this->hasMany('App\Models\ConferenceQueue'); }

    /**
     * 保存会议室
     *
     * @param array $data
     * @return bool
     */
    static function store(array $data) {

        $cr = self::create($data);
        
        return $cr ? true : false;

    }

    /**
     * 更新会议室
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    static function modify(array $data, $id) {

        $cr = self::find($id);
        if (!$cr) { return false; }
        
        return $cr->update($data) ? true : false;

    }
    
    /**
     * 删除会议室
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    static function remove($id) {

        $cr = self::find($id);
        if (!$cr) { return false; }
        
        return self::removable($id) ? $cr->delete() : false;

    }

    static function datatable() {

        $columns = [
            ['db' => 'ConferenceRoom.id', 'dt' => 0],
            ['db' => 'ConferenceRoom.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'ConferenceRoom.capacity', 'dt' => 3],
            ['db' => 'ConferenceRoom.remark', 'dt' => 4],
            ['db' => 'ConferenceRoom.created_at', 'dt' => 5],
            ['db' => 'ConferenceRoom.updated_at', 'dt' => 6],
            [
                'db' => 'ConferenceRoom.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = ConferenceRoom.school_id',
                ],
            ],
        ];
        $condition = 'ConferenceRoom.school_id = ' . School::id();
        
        return Datatable::simple(ConferenceRoom::getModel(), $columns, $joins,$condition);

    }

}
