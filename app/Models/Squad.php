<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Squad
 *
 * @property int $id
 * @property int $grade_id 所属年级ID
 * @property string $name 班级名称
 * @property string $educator_ids 班主任教职员工ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Squad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Squad whereEducatorIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Squad whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Squad whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Squad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Squad whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Squad whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Squad extends Model {

    protected $table = 'classes';

}
