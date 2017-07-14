<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|Operator whereCompanyId($value)
 * @method static Builder|Operator whereCreatedAt($value)
 * @method static Builder|Operator whereId($value)
 * @method static Builder|Operator whereSchoolIds($value)
 * @method static Builder|Operator whereType($value)
 * @method static Builder|Operator whereUpdatedAt($value)
 * @method static Builder|Operator whereUserId($value)
 * @mixin \Eloquent
 */
class Operator extends Model {
    //
    protected $table = 'operators';

    protected $fillable = ['company_id','user_id','school_id','type','created_at','updated_at'];
}
