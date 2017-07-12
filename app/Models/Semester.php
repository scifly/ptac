<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Semester
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $name 学期名称
 * @property string $start_date 学期开始日期
 * @property string $end_date 学期截止日期
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Semester whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Semester whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Semester whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Semester whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Semester whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Semester whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Semester whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Semester whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Semester extends Model
{
    //
}
