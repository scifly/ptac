<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\SubjectRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Subject 科目
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $name 科目名称
 * @property int $isaux 是否为副科
 * @property int $max_score 科目满分
 * @property int $pass_score 及格分数
 * @property string $grade_ids 年级ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
 * @mixin Eloquent
 * @property-read Grade $grade
 * @property-read School $school
 * @property-read Collection|SubjectModule[] $subjectModules
 * @property-read EducatorClass $educatorClass
 * @property-read Collection|Major[] $majors
 * @property-read Collection|Event[] $events
 * @property-read Collection|Squad[] $classes
 * @property-read Collection|Educator[] $educators
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $scores
 */
class Subject extends Model {
    
    use ModelTrait;
    
    protected $table = 'subjects';
    
    protected $fillable = [
        'school_id', 'name', 'isaux',
        'max_score', 'pass_score', 'grade_ids',
        'enabled',
    ];
    
    /**
     * 返回指定科目所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定科目包含的所有科目次分类对象
     *
     * @return HasMany
     */
    function subjectModules() { return $this->hasMany('App\Models\SubjectModule'); }
    
    /**
     * 获取指定科目包含的所有专业对象
     *
     * @return BelongsToMany
     */
    function majors() {
        
        return $this->belongsToMany(
            'App\Models\Major',
            'majors_subjects',
            'subject_id',
            'major_id'
        );
        
    }
    
    /**
     * 获取指定科目包含的所有事件对象
     *
     * @return HasMany
     */
    function events() { return $this->hasMany('App\Models\Event'); }
    
    /**
     * 获取指定科目对应的所有班级对象
     *
     * @return BelongsToMany
     */
    function classes() {
        
        return $this->belongsToMany(
            'App\Models\Squad',
            'educators_classes',
            'subject_id',
            'class_id'
        );
        
    }
    
    /**
     * 获取指定科目对应的所有教职员工对象
     *
     * @return BelongsToMany
     */
    function educators() {
        
        return $this->belongsToMany(
            'App\Models\Educator',
            'educators_classes',
            'subject_id',
            'educator_id'
        );
        
    }
    
    /**
     * 获取指定科目对应的所有分数对象
     *
     * @return HasMany
     */
    function scores() { return $this->hasMany('App\Models\Score'); }
    
    /**
     * 科目列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Subject.id', 'dt' => 0],
            [
                'db'        => 'Subject.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-book', '') . $d;
                },
            ],
            [
                'db'        => 'Subject.isaux', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d
                        ? sprintf(Snippet::BADGE_GREEN, '是')
                        : sprintf(Snippet::BADGE_GREEN, '否');
                    
                },
            ],
            ['db' => 'Subject.max_score', 'dt' => 3],
            ['db' => 'Subject.pass_score', 'dt' => 4],
            ['db' => 'Subject.created_at', 'dt' => 5],
            ['db' => 'Subject.updated_at', 'dt' => 6],
            [
                'db'        => 'Subject.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $id);
                    $delLink = sprintf(Snippet::DT_LINK_DEL, $id);
                    
                    return Snippet::status($d) . $editLink . $delLink;
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
        $condition = 'Subject.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存新的科目记录
     *
     * @param SubjectRequest $request
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function store(SubjectRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                $subject = self::create([
                    'name'       => $request->input('name'),
                    'school_id'  => $request->input('school_id'),
                    'max_score'  => $request->input('max_score'),
                    'pass_score' => $request->input('pass_score'),
                    'grade_ids'  => $request->input('grade_ids'),
                    'isaux'      => $request->input('isaux'),
                    'enabled'    => $request->input('enabled'),
                ]);
                if (!empty($request->input('major_ids'))) {
                    (new MajorSubject)->storeBySubjectId(
                        $subject->id, $request->input('major_ids')
                    );
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新指定的科目记录
     *
     * @param SubjectRequest $request
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function modify(SubjectRequest $request, $id) {
        
        try {
            DB::transaction(function () use ($request, $id) {
                $subject = $this->find($id);
                $subject->update([
                    'name'       => $request->input('name'),
                    'school_id'  => $request->input('school_id'),
                    'max_score'  => $request->input('max_score'),
                    'pass_score' => $request->input('pass_score'),
                    'grade_ids'  => $request->input('grade_ids'),
                    'isaux'      => $request->input('isaux'),
                    'enabled'    => $request->input('enabled'),
                ]);
                MajorSubject::whereSubjectId($id)->delete();
                if (!empty($request->input('major_ids'))) {
                    (new MajorSubject)->storeBySubjectId(
                        $id, $request->input('major_ids')
                    );
                }
            });
            
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除指定的科目记录
     *
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 从科目中移除已删除的年级
     *
     * @param $gradeId
     * @throws Throwable
     */
    function removeGrade($gradeId) {
        
        try {
            DB::transaction(function () use ($gradeId) {
                $subjects = $this->whereRaw($gradeId . ' IN (grade_ids)')->get();
                foreach ($subjects as $subject) {
                    $grade_ids = implode(
                        ',', array_diff(explode(',', $subject->grade_ids), [$gradeId])
                    );
                    $subject->update(['grade_ids' => $grade_ids]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 删除指定科目的所有记录
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                EducatorClass::whereSubjectId($id)->delete();
                Event::whereSubjectId($id)->delete();
                (new Exam)->removeSubject($id);
                SubjectModule::whereSubjectId($id)->delete();
                MajorSubject::whereSubjectId($id)->delete();
                (new ScoreRange)->removeSubject($id);
                (new Score)->removeSubject($id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
