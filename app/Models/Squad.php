<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\SquadRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Squad 班级
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
 * @property-read \App\Models\Grade $grade
 * @property-read Collection|Student[] $students
 * @property-read Collection|EducatorClass[] $educatorClass
 * @property-read Collection|Educator[] $educators
 */
class Squad extends Model {
    
    protected $table = 'classes';
    
    protected $fillable = [
        'id', 'grade_id', 'name',
        'educator_ids', 'enabled',
    ];
    
    /**
     * 返回指定班级所属的年级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade() { return $this->belongsTo('App\Models\Grade'); }
    
    /**
     * 获取指定班级包含的所有学生对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students() { return $this->hasMany('App\Models\Student'); }
    
    /**
     * 获取指定班级包含的所有教职员工对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function educators() {
        
        return $this->belongsToMany('App\Models\Educator', 'educators_classes');
        
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
