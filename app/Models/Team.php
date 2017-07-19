<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Team
 *
 * @property int $id
 * @property string $name 教职员工组名称
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Team whereCreatedAt($value)
 * @method static Builder|Team whereEnabled($value)
 * @method static Builder|Team whereId($value)
 * @method static Builder|Team whereName($value)
 * @method static Builder|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 * 教师员工组
 */
class Team extends Model {
    //
    protected $table = 'teams';
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

}
