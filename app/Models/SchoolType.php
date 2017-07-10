<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SchoolType
 *
 * @property int $id
 * @property string $name 学校类型名称
 * @property string $remark 学校类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SchoolType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SchoolType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SchoolType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SchoolType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SchoolType whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SchoolType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SchoolType extends Model
{
    //
}
