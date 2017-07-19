<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Educator
 *
 * @property int $id
 * @property int $user_id 教职员工用户ID
 * @property string $team_ids 所属组
 * @property int $school_id 所属学校ID
 * @property int $sms_quote 可用短信条数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static Builder|Educator whereCreatedAt($value)
 * @method static Builder|Educator whereId($value)
 * @method static Builder|Educator whereSchoolId($value)
 * @method static Builder|Educator whereSmsQuote($value)
 * @method static Builder|Educator whereTeamIds($value)
 * @method static Builder|Educator whereUpdatedAt($value)
 * @method static Builder|Educator whereUserId($value)
 * @mixin \Eloquent
 */
class Educator extends Model
{
    protected $table = 'educators';
    protected $fillable = [
        'user_id',
        'team_ids',
        'school_id',
        'sms_quote',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 教职工与所属组  多对多
     */
    public function teams()
    {
        return $this->belongsToMany('App\Models\Team');
    }

    /**
     * 教职员工与学校 反向一对多
     */
    public function school()
    {
        return $this->belongsTo('App\Models\School');
    }

    /**
     * 教职员工与班级 多对多
     */
    public function classes()
    {
        return $this->belongsToMany('App\Models\Squad', 'EducatorClass', 'educator_id', 'class_id');
    }

    /**
     * 教职员工与科目 多对多
     */
    public function sujectes()
    {
        return $this->belongsToMany('App\Models\Subject','EducatorClass','educator_id','subject_id');
    }
}
