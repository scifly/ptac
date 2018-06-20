<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\EducatorTeam 教职员工与教职员工组关系
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property int $team_id 教职员工组ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|EducatorTeam whereCreatedAt($value)
 * @method static Builder|EducatorTeam whereEducatorId($value)
 * @method static Builder|EducatorTeam whereEnabled($value)
 * @method static Builder|EducatorTeam whereId($value)
 * @method static Builder|EducatorTeam whereTeamId($value)
 * @method static Builder|EducatorTeam whereUpdatedAt($value)
 * @mixin Eloquent
 */
class EducatorTeam extends Model {

    protected $table = 'educators_teams';

    protected $fillable = ['educator_id', 'team_id', 'enabled'];

}