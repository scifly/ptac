<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Department
 *
 * @property int $id
 * @property int|null $parent_id 父部门ID
 * @property int $corp_id 所属企业ID
 * @property int $school_id 所属学校ID
 * @property string $name 部门名称
 * @property string|null $remark 部门备注
 * @property int|null $order 在父部门中的次序值。order值大的排序靠前
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Department whereCorpId($value)
 * @method static Builder|Department whereCreatedAt($value)
 * @method static Builder|Department whereEnabled($value)
 * @method static Builder|Department whereId($value)
 * @method static Builder|Department whereName($value)
 * @method static Builder|Department whereOrder($value)
 * @method static Builder|Department whereParentId($value)
 * @method static Builder|Department whereRemark($value)
 * @method static Builder|Department whereSchoolId($value)
 * @method static Builder|Department whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Corp $corp
 * @property-read \App\Models\School $school
 */
class Department extends Model
{
    //
    protected $table = 'departments';
    protected $fillable = [
        'praent_id',
        'corp_id',
        'school_id',
        'name',
        'remark',
        'order',
        'enabled'
    ];

    /**
     * 部门与所属企业
     */
    public function corp()
    {
        return $this->belongsTo('App\Models\Corp');
    }

    /**
     * 部门与所属学校
     */
    public function school(){
        return $this->belongsTo('App\Models\School');
    }
}
