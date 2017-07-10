<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AlertType
 *
 * @property int $id
 * @property string $name 提前提醒的时间
 * @property string $english_name 提前提醒时间的英文名称
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertType whereEnglishName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AlertType extends Model
{
    //
}
