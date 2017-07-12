<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamType
 *
 * @property int $id
 * @property string $name 考试类型名称
 * @property string $remark 考试类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamType whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExamType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamType extends Model
{
    //
}
