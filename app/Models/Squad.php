<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Http\Request;
/**
 * App\Models\Squad
 *
 * @property int $id
 * @property int $grade_id 所属年级ID
 * @property string $name 班级名称
 * @property string $educator_ids 班主任教职员工ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Squad whereCreatedAt($value)
 * @method static Builder|Squad whereEducatorIds($value)
 * @method static Builder|Squad whereEnabled($value)
 * @method static Builder|Squad whereGradeId($value)
 * @method static Builder|Squad whereId($value)
 * @method static Builder|Squad whereName($value)
 * @method static Builder|Squad whereUpdatedAt($value)
 * @mixin \Eloquent
 * 班级
 */
class Squad extends Model {
    
    protected $table = 'classes';
    protected $fillable = [
        'id',
        'grade_id',
        'name',
        'educator_ids',
        'enabled',
    ];
    public function students()
    {
        return $this->hasMany('App\Models\Student','class_id','id');
    }

    public function grade()
    {
        return $this->belongsTo('App\Models\Grade');
    }
    public function datatable(Request $request) {

        $columns = [
            ['db' => 'Squad.id', 'dt' => 0],
            ['db' => 'Squad.grade_id', 'dt' => 1],
            ['db' => 'Squad.name', 'dt' => 2],
            ['db' => 'Squad.educator_ids', 'dt' => 3],

            [
                'db' => 'Squad.enabled', 'dt' => 4,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];

        return Datatable::simple($this, $request, $columns);
    }
    
}
