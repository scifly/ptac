<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
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
 * @property int $school_id 所属学校ID
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Educator[] $educators
 * @property-read School $school
 * @method static Builder|Team whereCreatedAt($value)
 * @method static Builder|Team whereEnabled($value)
 * @method static Builder|Team whereId($value)
 * @method static Builder|Team whereName($value)
 * @method static Builder|Team whereRemark($value)
 * @method static Builder|Team whereSchoolId($value)
 * @method static Builder|Team whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Team extends Model {
    
    use ModelTrait;

    protected $fillable = ['name', 'school_id', 'remark', 'enabled'];

    /**
     * 返回指定教职员工组所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }

    /**
     * 获取指定教职员工组包含的所有教职员工对象
     *
     * @return BelongsToMany
     */
    function educators() { return $this->belongsToMany('App\Models\Educator', 'educators_teams'); }

    /**
     * 获取教职员工组列表
     *
     * @param array $teamIds
     * @return array
     */
    function teams(array $teamIds) {
        
        $teams = [];
        foreach ($teamIds as $id) {
            $team = self::find($id);
            $teams[$team->id] = $team['name'];
        }

        return $teams;

    }
    
    /**
     * 删除教职员工组
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        $team = $this->find($id);
        if (!$team) { return false; }
        $removed = $team->removable($team) ? $team->delete() : false;
        
        return $removed ?? false;
        
    }
    
    /**
     * 教职员工组列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Team.id', 'dt' => 0],
            ['db' => 'Team.name', 'dt' => 1],
            ['db' => 'Team.remark', 'dt' => 2],
            ['db' => 'Team.created_at', 'dt' => 3],
            ['db' => 'Team.updated_at', 'dt' => 4],
            [
                'db' => 'Team.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $condition = 'Team.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, null, $condition
        );

    }

}
