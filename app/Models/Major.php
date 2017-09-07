<?php

namespace App\Models;

use App\Http\Requests\MajorRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

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
            [
                'db' => 'Major.updated_at', 'dt' => 5,
                'formatter' => function($d, $row) {
                    return DataTable::dtOps($this, $d, $row);
                }
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Major.school_id'
                ]
            ]
        ];
        return DataTable::simple($this, $columns, $joins);
        
    }
    
    public function store(MajorRequest $request) {
    
        try {
            $exception = DB::transaction(function() use ($request) {
                $m = $this->create($request->all());
                $majorSubject = new MajorSubject();
                $subjectIds = $request->input('subject_ids', []);
                $majorSubject->storeByMajorId($m->id, $subjectIds);
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    public function modify(MajorRequest $request, $majorId) {
        
        $major = $this->find($majorId);
        if (!isset($major)) { return false; }
        try {
            $exception = DB::transaction(function() use($request, $majorId, $major) {
                $major->update($request->all());
                $subjectIds = $request->input('subject_ids', []);
                $majorSubject = new MajorSubject();
                $majorSubject::whereMajorId($majorId)->delete();
                $majorSubject->storeByMajorId($majorId, $subjectIds);
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    public function remove($majorId) {
        
        $major = $this->find($majorId);
        if (!isset($major)) { return false; }
        try {
            $exception = DB::transaction(function() use ($majorId, $major) {
                # 删除指定的专业记录
                $major->delete();
                # 删除与指定专业绑定的科目记录
                MajorSubject::whereMajorId($majorId)->delete();
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
}
