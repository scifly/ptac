<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceRoom whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceRoom whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceRoom whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceRoom whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceRoom whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConferenceRoom whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConferenceRoom extends Model
{
    //
}
