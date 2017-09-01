<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducatorTeam
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property int $team_id 教职员工组ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorTeam whereEducatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorTeam whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorTeam whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorTeam whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EducatorTeam extends Model {
    
    protected $table = 'educators_teams';
    
    protected $fillable = [
        'educator_id', 'team_id', 'enabled'
    ];
    
}
