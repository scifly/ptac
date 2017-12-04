<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\SubjectRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * App\Models\Subject
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $name 科目名称
 * @property int $isaux 是否为副科
 * @property int $max_score 科目满分
 * @property int $pass_score 及格分数
 * @property string $grade_ids 年级ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Subject whereCreatedAt($value)
 * @method static Builder|Subject whereEnabled($value)
 * @method static Builder|Subject whereGradeIds($value)
 * @method static Builder|Subject whereId($value)
 * @method static Builder|Subject whereIsaux($value)
 * @method static Builder|Subject whereMaxScore($value)
 * @method static Builder|Subject whereName($value)
 * @method static Builder|Subject wherePassScore($value)
 * @method static Builder|Subject whereSchoolId($value)
 * @method static Builder|Subject whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Grade $grade
 * @property-read School $school
 * @property-read Collection|SubjectModule[] $subjectModules
 * @property-read EducatorClass $educatorClass
 * @property-read Collection|Major[] $majors
 */
class Subject extends Model {
    
    protected $table = 'subjects';
    protected $fillable = [
        'school_id', 'name', 'isaux',
        'max_score', 'pass_score', 'grade_ids',
        'enabled',
    ];
    
    /**
     * 返回指定科目所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定科目包含的所有科目次分类对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjectModules() { return $this->hasMany('App\Models\SubjectModule'); }
    
    /**
     * 获取指定科目包含的所有专业对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function majors() {
        return $this->belongsToMany(
            'App\Models\Major',
            'majors_subjects',
            'subject_id',
            'major_id'
        );
        
    }
    
    /**
     * 获取指定学校的科目列表
     *
     * @param $schoolId
     * @return \Illuminate\Support\Collection
     */
    public function subjects($schoolId) {
        return $this->where('school_id', $schoolId)->get()->pluck('id', 'name');
        
    }
    
    /**
     * 获取指定成绩统计项包含的科目列表
     *
     * @param $ids
     * @return array
     */
    public function selectedSubjects($ids) {
        $ids = explode(',', $ids);
        $selectedSubjects = [];
        foreach ($ids as $id) {
            $selectedSubjects[$id] = $this->find($id)->name;
        }
        
        return $selectedSubjects;
        
    }
    
    /**
     * 保存新的科目记录
     *
     * @param SubjectRequest $request
     * @return bool
     */
    public function store(SubjectRequest $request) {
        try {
            $exception = DB::transaction(function () use ($request) {
                $subjectData = [
                    'name'       => $request->input('name'),
                    'school_id'  => $request->input('school_id'),
                    'max_score'  => $request->input('max_score'),
                    'pass_score' => $request->input('pass_score'),
                    'grade_ids'  => $request->input('grade_ids'),
                    'isaux'      => $request->input('isaux'),
                    'enabled'    => $request->input('enabled'),
                ];
                $subject = $this->create($subjectData);
                $majorSubject = new MajorSubject();
                $majorSubject->storeBySubjectId($subject->id, $request->input('major_ids'));
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 更新指定的科目记录
     *
     * @param SubjectRequest $request
     * @param $id
     * @return bool|mixed
     */
    public function modify(SubjectRequest $request, $id) {
        $subject = $this->find($id);
        if (!isset($subject)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($request, $id, $subject) {
                $subject->update([
                    'name'       => $request->input('name'),
                    'school_id'  => $request->input('school_id'),
                    'max_score'  => $request->input('max_score'),
                    'pass_score' => $request->input('pass_score'),
                    'grade_ids'  => $request->input('grade_ids'),
                    'isaux'      => $request->input('isaux'),
                    'enabled'    => $request->input('enabled'),
                ]);
                $majorSubject = new MajorSubject();
                $majorSubject::whereSubjectId($id)->delete();
                $majorSubject->storeBySubjectId($id, $request->input('major_ids'));
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 删除指定的科目记录
     *
     * @param $subjectId
     * @return bool|mixed
     */
    public function remove($subjectId) {
        $subject = $this->find($subjectId);
        if (!isset($subject)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($subjectId, $subject) {
                # 删除指定的科目记录
                $subject->delete();
                # 删除与科目绑定的专业记录
                MajorSubject::where('subject_id', $subjectId)->delete();
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    public function getId(array $subjects) {
        $result = [];
        foreach ($subjects as $v) {
            $result[$v] = $this->whereName($v)->value('id');
        }
        
        return $result;
    }
    
    public function datatable() {
        $columns = [
            ['db' => 'Subject.id', 'dt' => 0],
            [
                'db' => 'Subject.name', 'dt' => 1,
                'formatter' => function($d) {
                    return '<i class="fa fa-book"></i>&nbsp;' . $d;
                }
            ],
            [
                'db' => 'School.name as schoolname', 'dt' => 2,
                'formatter' => function($d) {
                    return '<i class="fa fa-university"></i>&nbsp;' . $d;
                }
            ],
            [
                'db'        => 'Subject.isaux', 'dt' => 3,
                'formatter' => function ($d) {
                    $subject = Subject::whereId($d)->first();
                    
                    return $subject->isaux == 1 ? '是' : '否';
                },
            ],
            ['db' => 'Subject.max_score', 'dt' => 4],
            ['db' => 'Subject.pass_score', 'dt' => 5],
            ['db' => 'Subject.created_at', 'dt' => 6],
            ['db' => 'Subject.updated_at', 'dt' => 7],
            [
                'db'        => 'Subject.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $status = $d ? Datatable::DT_ON : Datatable::DT_OFF;
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $id);
                    $delLink = sprintf(Datatable::DT_LINK_DEL, $id);
                    return
                        $status . str_repeat(Datatable::DT_SPACE, 3) .
                        $editLink . str_repeat(Datatable::DT_SPACE, 2) .
                        $delLink;
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => ['School.id = Subject.school_id'],
            ],
        ];
    
        $school = new School();
        $schoolId = $school->getSchoolId();
        $condition = 'Subject.school_id = ' . $schoolId;
        return Datatable::simple($this, $columns, $joins, $condition);
        
    }
    
}
