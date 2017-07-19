<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
 */
class ConferenceRoom extends Model {
    //
    protected $tables = 'conference_rooms';
    protected $fillable = [
        'name',
        'school_id',
        'capacity',
        'remark',
        'created_at',
        'updated_at',
        'enabled'
    ];

    /**
     * 会议地址与学校 反向一对多
     */
    public function schools(){
        return $this->belongsTo('\App\Models\School');
    }
}
