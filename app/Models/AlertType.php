<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|AlertType whereCreatedAt($value)
 * @method static Builder|AlertType whereEnabled($value)
 * @method static Builder|AlertType whereEnglishName($value)
 * @method static Builder|AlertType whereId($value)
 * @method static Builder|AlertType whereName($value)
 * @method static Builder|AlertType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AlertType extends Model {


}
