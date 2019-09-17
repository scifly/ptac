<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Evaluate
 *
 * @property int $id
 * @property int $student_id 学生id
 * @property int $indicator_id 考核项id
 * @property int $semester_id 学期id
 * @property int $educator_id 考核人教职员工id
 * @property int $amount 加/减分值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate whereEducatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate whereIndicatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evaluate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Evaluate extends Model {
    
    //
}
