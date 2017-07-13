<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProcedureType
 *
 * @property int $id
 * @property string $name 流程种类名称
 * @property string $remark 流程种类备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ProcedureType whereCreatedAt($value)
 * @method static Builder|ProcedureType whereEnabled($value)
 * @method static Builder|ProcedureType whereId($value)
 * @method static Builder|ProcedureType whereName($value)
 * @method static Builder|ProcedureType whereRemark($value)
 * @method static Builder|ProcedureType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProcedureType extends Model {
    //
}
