<?php

namespace App\Models;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * App\Models\ConferenceRoom
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ConferenceQueue[] $conferenceQueues
 * @property-read \App\Models\School $school
 */
class ConferenceRoom extends Model {
    
    protected $fillable = [
        'name', 'school_id', 'capacity',
        'remark', 'enabled'
    ];
    
    /**
     * 返回会议室所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() {
        
        return $this->belongsTo('\App\Models\School');
        
    }
    
    /**
     * 获取指定会议室的会议队列
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conferenceQueues() {
        
        return $this->hasMany('App\Models\ConferenceQueue');
        
    }
    
    public function remove($conferenceRoomId) {
        
        $conferenceRoom = $this->find($conferenceRoomId);
        if (!$conferenceRoom) { return false; }
        try {
            $exception = DB::transaction(function() use($conferenceRoom, $conferenceRoomId) {
                $conferenceRoom->delete();
                $conferenceQueues = ConferenceQueue::whereConferenceRoomId($conferenceRoomId)->get();
                foreach ($conferenceQueues as $queue) {
                    $queue->delete();
                }
            });
            return is_null($exception) ? true : false;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'ConferenceRoom.id', 'dt' => 0],
            ['db' => 'ConferenceRoom.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'ConferenceRoom.capacity', 'dt' => 3],
            ['db' => 'ConferenceRoom.remark', 'dt' => 4],
            ['db' => 'ConferenceRoom.created_at', 'dt' => 5],
            ['db' => 'ConferenceRoom.updated_at', 'dt' => 6],
            [
                'db' => 'ConferenceRoom.created_at', 'dt' => 7,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = ConferenceRoom.school_id'
                ]
            ]
        ];
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}
