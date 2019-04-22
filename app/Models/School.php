<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait, Snippet};
use App\Jobs\CreateSchool;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasManyThrough,
    Relations\HasOne};
use Illuminate\Support\Facades\{Auth, DB, Log, Request};
use Throwable;

/**
 * App\Models\School 学校
 *
 * @property int $id
 * @property int $school_type_id 学校类型ID
 * @property int $menu_id
 * @property string $name 学校名称
 * @property string $signature 签名
 * @property string $address 学校地址
 * @property string $user_ids 第三方接口对应的用户id列表
 * @property float|null $longitude 学校所处经度
 * @property float|null $latitude 学校所处纬度
 * @property int $corp_id 学校所属企业ID
 * @property int|null $sms_max_cnt 学校短信配额
 * @property int|null $sms_used 短信已使用量
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $department_id 对应的部门ID
 * @property-read Collection|Turnstile[] $turnstiles
 * @property-read Collection|Squad[] $classes
 * @property-read Collection|SubjectModule[] $subjectModules
 * @property-read Collection|ConferenceRoom[] $conferenceRooms
 * @property-read Corp $corp
 * @property-read Department $department
 * @property-read Collection|Educator[] $educators
 * @property-read Collection|ExamType[] $examTypes
 * @property-read Collection|Exam[] $exams
 * @property-read Collection|Grade[] $grades
 * @property-read Collection|Group[] $groups
 * @property-read Collection|Major[] $majors
 * @property-read Menu $menu
 * @property-read Collection|PollQuestionnaire[] $pollQuestionnaires
 * @property-read Collection|Procedure[] $procedures
 * @property-read SchoolType $schoolType
 * @property-read Collection|Semester[] $semesters
 * @property-read Collection|Subject[] $subjects
 * @property-read Collection|Tag[] $tags
 * @property-read WapSite $wapSite
 * @property-read Collection|WapSiteModule[] $wapSiteModules
 * @method static Builder|School whereAddress($value)
 * @method static Builder|School whereCorpId($value)
 * @method static Builder|School whereCreatedAt($value)
 * @method static Builder|School whereDepartmentId($value)
 * @method static Builder|School whereEnabled($value)
 * @method static Builder|School whereId($value)
 * @method static Builder|School whereLatitude($value)
 * @method static Builder|School whereLongitude($value)
 * @method static Builder|School whereMenuId($value)
 * @method static Builder|School whereName($value)
 * @method static Builder|School whereSchoolTypeId($value)
 * @method static Builder|School whereSignature($value)
 * @method static Builder|School whereSmsMaxCnt($value)
 * @method static Builder|School whereSmsUsed($value)
 * @method static Builder|School whereUpdatedAt($value)
 * @method static Builder|School whereUserIds($value)
 * @method static Builder|School newModelQuery()
 * @method static Builder|School newQuery()
 * @method static Builder|School query()
 * @mixin Eloquent
 * @property-read Collection|PassageLog[] $passageLogs
 * @property-read Collection|PassageRule[] $passageRules
 */
class School extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'address', 'school_type_id', 'menu_id', 'signature', 'corp_id',
        'longitude', 'latitude', 'department_id', 'user_ids', 'enabled',
    ];
    
    /**
     * 返回对应的部门对象
     *
     * @return BelongsTo
     */
    function department() { return $this->belongsTo('App\Models\Department'); }
    
    /**
     * 返回对应的菜单对象
     *
     * @return BelongsTo
     */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /**
     * 返回所属学校类型对象
     *
     * @return BelongsTo
     */
    function schoolType() { return $this->belongsTo('App\Models\SchoolType'); }
    
    /**
     * 返回所属企业对象
     *
     * @return BelongsTo
     */
    function corp() { return $this->belongsTo('App\Models\Corp'); }
    
    /**
     * 获取隶属指定学校的所有角色对象
     *
     * @return HasMany
     */
    function groups() { return $this->hasMany('App\Models\Group'); }
    
    /**
     * 获取指定学校所有的考勤机对象
     *
     * @return HasMany
     */
    function turnstiles() { return $this->hasMany('App\Models\Turnstile'); }
    
    /**
     * 获取所有的会议室对象
     *
     * @return HasMany
     */
    function conferenceRooms() { return $this->hasMany('App\Models\ConferenceRoom'); }
    
    /**
     * 获取指定学校的所有调查问卷对象
     *
     * @return HasMany
     */
    function pollQuestionnaires() { return $this->hasMany('App\Models\PollQuestionnaire'); }
    
    /**
     * 获取指定学校的所有审批流程对象
     *
     * @return HasMany
     */
    function procedures() { return $this->hasMany('App\Models\Procedure'); }
    
    /**
     * 获取指定学校所有的学期对象
     *
     * @return HasMany
     */
    function semesters() { return $this->hasMany('App\Models\Semester'); }
    
    /**
     * 获取指定学校所有的科目对象
     *
     * @return HasMany
     */
    function subjects() { return $this->hasMany('App\Models\Subject'); }
    
    /**
     * 获取指定学校的所有教职员工组对象
     *
     * @return HasMany
     */
    function tags() { return $this->hasMany('App\Models\Tag'); }
    
    /**
     * 获取指定学校的所有年级对象
     *
     * @return HasMany
     */
    function grades() { return $this->hasMany('App\Models\Grade'); }
    
    /**
     * 获取指定学校的所有专业对象
     *
     * @return HasMany
     */
    function majors() { return $this->hasMany('App\Models\Major'); }
    
    /**
     * 获取指定学校包含的所有考试类型对象
     *
     * @return HasMany
     */
    function examTypes() { return $this->hasMany('App\Models\ExamType'); }
    
    /**
     * 获取指定学校包含的所有考试对象
     *
     * @return HasManyThrough
     */
    function exams() {
        
        return $this->hasManyThrough('App\Models\Exam', 'App\Models\ExamType');
        
    }
    
    /**
     * 获取指定学校所有的教职员工对象
     *
     * @return HasMany
     */
    function educators() { return $this->hasMany('App\Models\Educator'); }
    
    /**
     * 获取指定学校的微网站对象
     *
     * @return HasOne
     */
    function wapSite() { return $this->hasOne('App\Models\WapSite'); }
    
    /**
     * 通过WapSite中间对象获取所有的微网站模块对象
     *
     * @return HasManyThrough
     */
    function wapSiteModules() {
        
        return $this->hasManyThrough('App\Models\WapSiteModule', 'App\Models\WapSite');
        
    }
    
    /**
     * 通过Grade中间对象获取所有的班级对象
     *
     * @return HasManyThrough
     */
    function classes() {
        
        return $this->hasManyThrough(
            'App\Models\Squad', 'App\Models\Grade',
            'school_id', 'grade_id'
        );
        
    }
    
    /**
     * 获取指定学校的门禁通行记录
     *
     * @return HasMany
     */
    function passageLogs() { return $this->hasMany('App\Models\PassageLog'); }
    
    /**
     * 获取指定学校的门禁通行规则
     *
     * @return HasMany
     */
    function passageRules() { return $this->hasMany('App\Models\PassageRule'); }
    
    /**
     * 通过Subject中间对象获取所有的科目次分类对象
     *
     * @return HasManyThrough
     */
    function subjectModules() {
        
        return $this->hasManyThrough(
            'App\Models\SubjectModule', 'App\Models\Subject',
            'school_id', 'subject_id'
        );
        
    }
    
    /**
     * 学校列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'School.id', 'dt' => 0],
            [
                'db'        => 'School.name as schoolname', 'dt' => 1,
                'formatter' => function ($d) {
                    return Snippet::icon($d, 'school');
                },
            ],
            ['db' => 'School.address', 'dt' => 2],
            ['db' => 'SchoolType.name as typename', 'dt' => 3],
            [
                'db'        => 'Corp.name as corpname', 'dt' => 4,
                'formatter' => function ($d) {
                    return Snippet::icon($d, 'corp');
                },
            ],
            ['db' => 'School.created_at', 'dt' => 5],
            ['db' => 'School.updated_at', 'dt' => 6],
            [
                'db'        => 'Department.synced as synced', 'dt' => 7,
                'formatter' => function ($d) {
                    return $this->synced($d);
                },
            ],
            [
                'db'        => 'School.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'school_types',
                'alias'      => 'SchoolType',
                'type'       => 'INNER',
                'conditions' => [
                    'SchoolType.id = School.school_type_id',
                ],
            ],
            [
                'table'      => 'corps',
                'alias'      => 'Corp',
                'type'       => 'INNER',
                'conditions' => [
                    'Corp.id = School.corp_id',
                ],
            ],
            [
                'table'      => 'departments',
                'alias'      => 'Department',
                'type'       => 'INNER',
                'conditions' => [
                    'Department.id = School.department_id',
                ],
            ],
        ];
        # 仅在企业级显示学校列表
        $rootMenuId = (new Menu)->rootId(true);
        $condition = 'Corp.id = ' . Corp::whereMenuId($rootMenuId)->first()->id;
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存学校
     *
     * @param array $data
     * @return mixed|bool|null
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建学校、对应的部门和菜单
                $school = $this->create($data);
                $department = (new Department)->stow($school, 'corp');
                $menu = (new Menu)->stow($school, 'corp');
                # 更新学校的部门id和菜单id
                $school->update([
                    'department_id' => $department->id,
                    'menu_id'       => $menu->id,
                ]);
                # 创建学校后台管理菜单、菜单卡片绑定关系和微网站
                CreateSchool::dispatch($school, Auth::id());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新学校
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                if ($id) {
                    $school = $this->find($id);
                    $corpChanged = $school->corp_id != $data['corp_id'];
                    abort_if(
                        $corpChanged && !$this->removable($school),
                        HttpStatusCode::INTERNAL_SERVER_ERROR,
                        __('messages.school.corp_changed')
                    );
                    if (!$corpChanged) {
                        $school->update($data);
                        (new Department)->alter($school, 'corp');
                        (new Menu)->alter($school, 'corp');
                    } else {
                        unset($data['id']);
                        $this->create($data);
                        $this->remove($id);
                    }
                } else {
                    $this->batch($this);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除学校
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                session(['schoolId' => $id]);
                $classes = [
                    'Department', 'Menu', 'Educator',
                    'ConferenceRoom', 'ComboType', 'ExamType',
                    'Grade', 'Group', 'Major', 'Module',
                    'PollQuestionnaire', 'Procedure', 'Semester',
                    'Subject', 'Tag', 'WapSite',
                ];
                array_map(
                    function ($class) use ($ids) {
                        $model = $this->model($class);
                        $isDM = in_array($class, ['Department', 'Menu']);
                        $obj = $isDM ? $this : $model;
                        $_id = $isDM ? 'id' : 'school_id';
                        $field = $isDM ? ($class == 'Menu' ? 'menu_id' : 'department_id') : 'id';
                        $foreignIds = $obj->whereIn($_id, $ids)->pluck($field)->toArray();
                        if (!empty($foreignIds)) {
                            Request::replace(['ids' => $foreignIds]);
                            $model->remove();
                        }
                        Log::debug($class . ' : ' . json_encode($ids));
                    }, $classes
                );
                Request::replace(['ids' => $ids]);
                $this->purge(['School'], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回对当前用户可见的所有年级列表html
     *
     * @return array
     */
    function gradeList() {
        
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->pluck('name', 'id')->toArray();
        reset($grades);
        
        return [
            $this->singleSelectList($grades, 'grade_id'),
            key($grades),
        ];
        
    }
    
    /**
     * 获取字段列表
     *
     * @param $field
     * @param $id
     * @param null $gradeClass
     * @return array
     */
    function fieldLists($field, $id, $gradeClass = null) {
        
        # todo - fetch field list by role
        $grades = [];
        $classes = [];
        $students = [];
        switch ($field) {
            case 'grade':
                if (isset($gradeClass)) {
                    list($classes, $students) = $this->getClass($id, $gradeClass);
                } else {
                    $classes = Squad::whereGradeId($id)
                        ->where('enabled', 1)
                        ->pluck('name', 'id');
                    $students = Student::whereClassId($classes->keys()->first())
                        ->where('enabled', 1)
                        ->pluck('sn', 'id');
                }
                break;
            case 'class':
                $list = Student::whereClassId($id)
                    ->where('enabled', 1)
                    ->get();
                if (!empty($list)) {
                    foreach ($list as $s) {
                        $students[$s->id] = $s->user->realname . "-" . $s->sn;
                    }
                }
                break;
            default:
                break;
        }
        $htmls = array_map(
            function ($items) {
                $html = '<select class="form-control col-sm-6" id="%s" name="%s">';
                foreach ($items as $key => $value) {
                    $html .= '<option value="' . $key . '">' . $value . '</option>';
                }
                $html .= '</select>';
                
                return $html;
            }, [$grades, $classes, $students]
        );
        
        return [
            'grades'   => sprintf($htmls[0], 'gradeId', 'gradeId'),
            'classes'  => sprintf($htmls[1], 'classId', 'classId'),
            'students' => sprintf($htmls[2], 'studentId', 'studentId'),
        ];
        
    }
    
    /**
     * 获取指定年级包含的的班级和学生列表
     *
     * @param $gradeId
     * @param $gradeClass
     * @return array
     */
    function getClass($gradeId, $gradeClass): array {
        
        $classes = $students = [];
        foreach ($gradeClass as $k => $g) {
            if ($k == $gradeId) {
                $classes = Squad::whereEnabled(1)
                    ->whereIn('id', $g)
                    ->pluck('name', 'id')
                    ->toArray();
                foreach ($g as $v) {
                    $students = Student::whereClassId($v)
                        ->where('enabled', 1)
                        ->pluck('sn', 'id');
                    break;
                }
            }
        }
        
        return [$classes, $students];
        
    }
    
}
