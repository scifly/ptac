<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Team 教职员工组
 *
 * @property int $id
 * @property string $name 教职员工组名称
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Team whereCreatedAt($value)
 * @method static Builder|Team whereEnabled($value)
 * @method static Builder|Team whereId($value)
 * @method static Builder|Team whereName($value)
 * @method static Builder|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $school_id 所属学校ID
 * @property string|null $remark 备注
 * @method static Builder|Team whereRemark($value)
 * @method static Builder|Team whereSchoolId($value)
 * @property-read Collection|Educator[] $educators
 * @property-read School $school
 */
class Team extends Model {

    protected $fillable = ['name', 'school_id', 'remark', 'enabled'];

    /**
     * 返回指定教职员工组所属的学校对象
     *
     * @return BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }

    /**
     * 获取指定教职员工组包含的所有教职员工对象
     *
     * @return BelongsToMany
     */
    public function educators() { return $this->belongsToMany('App\Models\Educator', 'educators_teams'); }

    /**
     * 获取教职员工组列表
     *
     * @param array $teamIds
     * @return array
     */
    public function teams(array $teamIds) {
        
        $teams = [];
        foreach ($teamIds as $id) {
            $team = $this->find($id);
            $teams[$team['id']] = $team['name'];
        }

        return $teams;

    }

    public function datatable() {
        
        $columns = [
            ['db' => 'Team.id', 'dt' => 0],
            ['db' => 'Team.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'Team.remark', 'dt' => 3],
            ['db' => 'Team.created_at', 'dt' => 4],
            ['db' => 'Team.updated_at', 'dt' => 5],
            [
                'db' => 'Team.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Team.school_id',
                ],
            ],
        ];
        
        return Datatable::simple($this, $columns, $joins);

    }

}
