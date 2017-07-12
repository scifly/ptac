<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Procedure
 *
 * @property int $id
 * @property int $procedure_type_id 流程类型ID
 * @property int $school_id 流程所属学校ID
 * @property string $name 流程名称
 * @property string $remark 流程备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Procedure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Procedure whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Procedure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Procedure whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Procedure whereProcedureTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Procedure whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Procedure whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Procedure whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Procedure extends Model
{
    //
}
