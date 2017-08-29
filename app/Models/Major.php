<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\Major
 *
 * @property int $id
 * @property string $name 专业名称
 * @property string $remark 专业备注
 * @property int $school_id 所属学校ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $enabled
 * @method static Builder|Major whereCreatedAt($value)
 * @method static Builder|Major whereEnabled($value)
 * @method static Builder|Major whereId($value)
 * @method static Builder|Major whereName($value)
 * @method static Builder|Major whereRemark($value)
 * @method static Builder|Major whereSchoolId($value)
 * @method static Builder|Major whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read MajorSubject $majorSubject
 * @property-read School $school
 * @property-read Collection|Subject[] $subjects
 */
class Major extends Model {

    protected $table = 'majors';
    
    protected $fillable = [
        'name', 'remark', 'school_id',
        'created_at', 'updated_at', 'enabled'
    ];
    
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    public function subjects() {
        
        return $this->belongsToMany(
            'App\Models\Subject',
            'majors_subjects',
            'major_id',
            'subject_id'
        );
        
    }
    
    public function majors($schoolId = NULL) {
        
        if (isset($schoolId)) {
            return $this->where('school_id', $schoolId)->get()->pluck('id', 'name');
        }
        return $this->pluck('id', 'name');
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Major.id', 'dt' => 0],
            ['db' => 'Major.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'Major.remark', 'dt' => 3],
            ['db' => 'Major.created_at', 'dt' => 4],
            ['db' => 'Major.updated_at', 'dt' => 5],
            [
                'db' => 'Major.id', 'dt' => 0,
                'formatter' => function($d, $row) {
                    return DataTable::dtOps($this, $d, $row);
                }
            ],
        ];
        $joins = [
            'table' => 'schools',
            'alias' => 'School',
            'type' => 'INNER',
            'conditions' => [
                'School.id = Major.school_id'
            ]
        ];
        return DataTable::simple($this, $columns, $joins);
        
    }
    
    
}
