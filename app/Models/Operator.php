<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Operator
 *
 * @property int $id
 * @property int $company_id 所属运营者公司ID
 * @property int $user_id 用户ID
 * @property string $school_ids 可管理的学校ID
 * @property int $type 管理员类型：0 - 我们 1 - 代理人
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operator whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operator whereSchoolIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operator whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operator whereUserId($value)
 * @mixin \Eloquent
 */
class Operator extends Model
{
    //
}
