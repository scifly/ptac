<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CommType
 *
 * @property int $id
 * @property string $name 通信方式名称
 * @property string $remark 通信方式备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|CommType whereCreatedAt($value)
 * @method static Builder|CommType whereEnabled($value)
 * @method static Builder|CommType whereId($value)
 * @method static Builder|CommType whereName($value)
 * @method static Builder|CommType whereRemark($value)
 * @method static Builder|CommType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommType extends Model {
    //
}
