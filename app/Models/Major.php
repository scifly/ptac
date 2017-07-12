<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Major
 *
 * @property int $id
 * @property string $name 专业名称
 * @property string $remark 专业备注
 * @property int $school_id 所属学校ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Major whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Major whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Major whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Major whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Major whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Major whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Major whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Major extends Model
{
    //
}
