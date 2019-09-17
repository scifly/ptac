<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Indicator
 *
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 名称
 * @property int $sign 0 - 减分，1 - 加分
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator whereSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Indicator whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Indicator extends Model {
    
    //
}
