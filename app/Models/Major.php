<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\MajorRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
        'name', 'remark', 'school_id', 'enabled'
    ];
    
    /**
     * 返回专业所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定专业所包含的科目对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects() {
        
        return $this->belongsToMany(
            'App\Models\Subject',
            'majors_subjects',
            'major_id',
            'subject_id'
        );
        
    }
    
    /**
     * 返回专业列表
     *
     * @param null $schoolId
     * @return \Illuminate\Support\Collection
     */
    public function majors($schoolId = NULL) {
        
        if (isset($schoolId)) {
            return $this->where('school_id', $schoolId)->get()->pluck('id', 'name');
        }
        return $this->pluck('id', 'name');
        
    }
    
    /**
     * 保存专业
     *
     * @param MajorRequest $request
     * @return bool|mixed
     */
    public function store(MajorRequest $request) {
        
        try {
            $exception = DB::transaction(function () use ($request) {
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
    
    /**
     * 更新专业
     *
     * @param MajorRequest $request
     * @param $id
     * @return bool|mixed
     */
    public function modify(MajorRequest $request, $id) {
        
        $major = $this->find($id);
        if (!isset($major)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($request, $id, $major) {
                $major->update($request->all());
                $subjectIds = $request->input('subject_ids', []);
                $majorSubject = new MajorSubject();
                $majorSubject::whereMajorId($id)->delete();
                $majorSubject->storeByMajorId($id, $subjectIds);
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 删除专业
     *
     * @param $id
     * @return bool|mixed
     */
    public function remove($id) {
        
        $major = $this->find($id);
        if (!isset($major)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($id, $major) {
                # 删除指定的专业记录
                $major->delete();
                # 删除与指定专业绑定的科目记录
                MajorSubject::whereMajorId($id)->delete();
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
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
                'formatter' => function ($d, $row) {
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
    
}
