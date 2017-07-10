<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommType whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommType extends Model
{
    //
}
