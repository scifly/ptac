<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\SquadRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
 * @property-read \App\Models\Grade $grade
 * @property-read Collection|Student[] $students
 * @property-read Collection|EducatorClass[] $educatorClass
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
    
    public function students() { return $this->hasMany('App\Models\Student'); }
    
    public function grade() { return $this->belongsTo('App\Models\Grade'); }

    public function educators() {
        
        return $this->belongsToMany('App\Models\Educator', 'educators_classes');
        
    }
    
    public function existed(SquadRequest $request, $id = NULL) {
        
        if (!$id) {
            $class = $this->where([
                'grade_id' => $request->input('grade_id'),
                'name' => $request->input('name')
            ])->first();
        } else {
            $class = $this->where('grade_id', $request->input('grade_id'))
                ->where('id', '<>', $id)
                ->where('name', $request->input('name'))
                ->first();
        }
        return $class ? true : false;
        
    }
    
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Squad.id', 'dt' => 0],
            ['db' => 'Squad.name', 'dt' => 1],
            ['db' => 'Grade.name as gradename', 'dt' => 2],
            ['db' => 'Squad.educator_ids', 'dt' => 3],
            ['db' => 'Squad.created_at', 'dt' => 4],
            ['db' => 'Squad.updated_at', 'dt' => 5],
            
            [
                'db' => 'Squad.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'grades',
                'alias' => 'Grade',
                'type' => 'INNER',
                'conditions' => [
                    'Grade.id = Squad.grade_id'
                ]
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
    
}
