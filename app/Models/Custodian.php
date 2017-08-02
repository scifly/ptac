<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Custodian
 *
 * @property int $id
 * @property int $user_id 监护人用户ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $expiry 服务到期时间
 * @property-read \App\Models\User $user
 * @method static Builder|Custodian whereCreatedAt($value)
 * @method static Builder|Custodian whereExpiry($value)
 * @method static Builder|Custodian whereId($value)
 * @method static Builder|Custodian whereUpdatedAt($value)
 * @method static Builder|Custodian whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 */
class Custodian extends Model
{
    protected $table = 'custodians';
    protected $fillable = [
        'user_id',
        'expiry'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\Student');
    }

    public function custodianStudent()
    {
        return $this->hasOne('App\Models\CustodianStudent');
    }

}
