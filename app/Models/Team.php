<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
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
 * @property int $school_id 所属学校ID
 * @property string|null $remark 备注
 * @method static Builder|Team whereRemark($value)
 * @method static Builder|Team whereSchoolId($value)
 */
class Team extends Model {
    
    protected $fillable = [
        'name', 'enabled', 'school_id', 'remark'
    ];
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Team.id', 'dt' => 0],
            ['db' => 'Team.name', 'dt' => 1],
            ['db' => 'Team.remark', 'dt' => 2],
            ['db' => 'Team.created_at', 'dt' => 3],
            ['db' => 'Team.updated_at', 'dt' => 4],
            [
                'db' => 'Team.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        
        return Datatable::simple($this, $columns);
        
    }
    
}
