<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducatorTeam 教职员工与教职员工组
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property int $team_id 教职员工组ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|EducatorTeam whereCreatedAt($value)
 * @method static Builder|EducatorTeam whereEducatorId($value)
 * @method static Builder|EducatorTeam whereEnabled($value)
 * @method static Builder|EducatorTeam whereId($value)
 * @method static Builder|EducatorTeam whereTeamId($value)
 * @method static Builder|EducatorTeam whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EducatorTeam extends Model {
    
    protected $table = 'educators_teams';
    
    protected $fillable = ['educator_id', 'team_id', 'enabled'];
    
}
