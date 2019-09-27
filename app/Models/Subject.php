<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};
use Illuminate\Support\Facades\{DB, Request};
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
 * @property-read Grade $grade
 * @property-read School $school
 * @property-read Collection|SubjectModule[] $subjectModules
 * @property-read ClassEducator $educatorClass
 * @property-read Collection|Major[] $majors
 * @property-read Collection|Event[] $events
 * @property-read Collection|Squad[] $classes
 * @property-read Collection|Educator[] $educators
 * @property-read Collection|Score[] $scores
 * @property-read int|null $classes_count
 * @property-read int|null $educators_count
 * @property-read int|null $events_count
 * @property-read int|null $majors_count
 * @property-read int|null $scores_count
 * @property-read int|null $subject_modules_count
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
 * @method static Builder|Subject newModelQuery()
 * @method static Builder|Subject newQuery()
 * @method static Builder|Subject query()
 * @mixin Eloquent
 * @property-read Collection|SubjectModule[] $modules
 * @property-read int|null $modules_count
 */
class Subject extends Model {
    
    use ModelTrait;
    
    protected $table = 'subjects';
    
    protected $fillable = [
        'school_id', 'name', 'isaux',
        'max_score', 'pass_score', 'grade_ids',
        'enabled',
    ];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
    function modules() { return $this->hasMany('App\Models\SubjectModule'); }
    
    /** @return BelongsToMany */
    function majors() { return $this->belongsToMany('App\Models\Major', 'major_subject'); }
    
    /** @return BelongsToMany */
    function classes() {
        
        return $this->belongsToMany(
            'App\Models\Squad',
            'class_educator',
            'subject_id',
            'class_id'
        );
        
    }
    
    /** @return BelongsToMany */
    function educators() {
        
        return $this->belongsToMany(
            'App\Models\Educator',
            'class_educator',
            'subject_id',
            'educator_id'
        );
        
    }
    
    /** @return HasMany */
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
                    return $this->iconHtml('fa-book') . $d;
                },
            ],
            [
                'db'        => 'Subject.isaux', 'dt' => 2,
                'formatter' => function ($d) {
                    return $this->badge(
                        $d ? 'text-green' : 'text-gray',
                        $d ? '是' : '否'
                    );
                },
            ],
            ['db' => 'Subject.max_score', 'dt' => 3],
            ['db' => 'Subject.pass_score', 'dt' => 4],
            ['db' => 'Subject.created_at', 'dt' => 5],
            ['db' => 'Subject.updated_at', 'dt' => 6],
            [
                'db'        => 'Subject.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
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
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存新的科目记录
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $subject = $this->create($data);
                $this->retain(
                    'MajorSubject', $subject->id,
                    $data['subject_ids'] ?? [], false
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新指定的科目记录
     *
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id,
            function (Subject $subject) use ($data, $id) {
                $subject->retain(
                    'MajorSubject', $id,
                    $data['subject_ids'] ?? [], false
                );
            }
        );
        
    }
    
    /**
     * 删除指定的科目记录
     *
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge(['Exam', 'ScoreRange'], 'subject_ids', 'clear', $id);
                $this->purge(['Score'], 'subject_id', 'reset', $id);
                $this->purge(
                    [class_basename($this), 'ClassEducator', 'Event', 'SubjectModule', 'MajorSubject'],
                    'subject_id', 'purge', $id
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @return array
     * @throws Exception
     */
    function compose() {
        
        if (explode('/', Request::path())[1] == 'index') {
            $data = [
                'titles' => ['#', '名称', '副科', '满分', '及格线', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $subject = Subject::find(Request::route('id'));
            $majors = $subject ? $subject->majors : null;
            $gradeIds = $subject ? $subject->grade_ids : null;
            
            $data = array_combine(
                ['grades', 'majors', 'selectedGrades', 'selectedMajors'],
                [
                    Grade::whereIn('id', $this->gradeIds())->pluck('name', 'id'),
                    Major::whereSchoolId($this->schoolId())->pluck('name', 'id'),
                    $majors ? $majors->pluck('name', 'id') : null,
                    !$gradeIds ? [] : Grade::whereIn('id', explode(',', $gradeIds))->pluck('id'),
                ]
            );
        }
        
        return $data;
        
    }
    
}
