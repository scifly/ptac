<?php
namespace App\Models;

use App\Events\SchoolCreated;
use App\Events\SchoolDeleted;
use App\Events\SchoolUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\School 学校
 *
 * @property int $id
 * @property int $school_type_id 学校类型ID
 * @property int $menu_id
 * @property string $name 学校名称
 * @property string $signature 签名
 * @property string $address 学校地址
 * @property float|null $longitude 学校所处经度
 * @property float|null $latitude 学校所处纬度
 * @property int $corp_id 学校所属企业ID
 * @property int|null $sms_max_cnt 学校短信配额
 * @property int|null $sms_used 短信已使用量
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $department_id 对应的部门ID
 * @property-read Collection|AttendanceMachine[] $attendanceMachines
 * @property-read Collection|Squad[] $classes
 * @property-read Collection|ConferenceRoom[] $conferenceRooms
 * @property-read Corp $corp
 * @property-read Department $department
 * @property-read Collection|Educator[] $educators
 * @property-read Collection|ExamType[] $examTypes
 * @property-read Collection|Grade[] $grades
 * @property-read Collection|Group[] $groups
 * @property-read Collection|Major[] $majors
 * @property-read Menu $menu
 * @property-read Collection|PollQuestionnaire[] $pollQuestionnaires
 * @property-read Collection|Procedure[] $procedures
 * @property-read SchoolType $schoolType
 * @property-read Collection|Semester[] $semesters
 * @property-read Collection|Subject[] $subjects
 * @property-read Collection|Team[] $teams
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
 * @mixin Eloquent
 */
class School extends Model {
    
    // todo: needs to be optimized
    use ModelTrait;
    
    protected $fillable = [
        'name', 'address', 'school_type_id', 'menu_id', 'signature',
        'corp_id', 'department_id', 'enabled',
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
    function attendanceMachines() { return $this->hasMany('App\Models\AttendanceMachine'); }
    
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
    function teams() { return $this->hasMany('App\Models\Team'); }
    
    /**
     * 获取指定学校所有的年级对象
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
     * 保存学校
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    function store(array $data, $fireEvent = false) {
        
        $school = $this->create($data);
        if ($school && $fireEvent) {
            event(new SchoolCreated($school));
            
            return true;
        }
        
        return $school ? true : false;
        
    }
    
    /**
     * 更新学校
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    function modify(array $data, $id, $fireEvent = false) {
        
        $school = $this->find($id);
        if (!$school) {
            return false;
        }
        $updated = $school->update($data);
        if ($updated && $fireEvent) {
            event(new SchoolUpdated($this->find($id)));
            
            return true;
        }
        
        return $updated ? true : false;
        
    }
    
    /**
     * 删除学校
     *
     * @param $id
     * @param bool $fireEvent
     * @return bool|null
     * @throws Exception
     */
    function remove($id, $fireEvent = false) {
        
        $school = $this->find($id);
        if (!$school) { return false; }
        $user = Auth::user();
        $role = $user->group->name;
        abort_if(
            !in_array($role, ['运营', '企业']) || !in_array($id, $this->schoolIds()),
            HttpStatusCode::FORBIDDEN,
            __('messages.forbidden')
        );
        $removed = $this->removable($school) ? $school->delete() : false;
        if ($removed && $fireEvent) {
            event(new SchoolDeleted($school));
            
            return true;
        }
        
        return $removed ? true : false;
        
    }
    
    /**
     * 学校列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'School.id', 'dt' => 0],
            [
                'db'        => 'School.name as schoolname', 'dt' => 1,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-university') . $d;
                },
            ],
            ['db' => 'School.address', 'dt' => 2],
            ['db' => 'SchoolType.name as typename', 'dt' => 3],
            [
                'db'        => 'Corp.name as corpname', 'dt' => 4,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-weixin') . $d;
                },
            ],
            ['db' => 'School.created_at', 'dt' => 5],
            ['db' => 'School.updated_at', 'dt' => 6],
            [
                'db'        => 'School.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
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
        ];
        # 仅在企业级显示学校列表
        $condition = 'Corp.id = ' . Corp::whereDepartmentId(Auth::user()->topDeptId())->first()->id;
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 获取字段列表
     *
     * @param $field
     * @param $id
     * @param null $gradeClass
     * @return array
     */
    function getFieldList($field, $id, $gradeClass = null) {
        
        # todo - fetch field list by role
        $grades = [];
        $classes = [];
        $students = [];
        switch ($field) {
            case 'school':
                $grades = Grade::whereSchoolId($id)
                    ->where('enabled', 1)
                    ->pluck('name', 'id');
                $classes = Squad::whereGradeId($grades->keys()->first())
                    ->where('enabled', 1)
                    ->pluck('name', 'id');
                $students = Student::whereClassId($classes->keys()->first())
                    ->where('enabled', 1)
                    ->pluck('student_number', 'id');
                break;
            case 'grade':
                if (isset($gradeClass)) {
                    $classes = $this->getClass($id, $gradeClass)[0];
                    $students = $this->getClass($id, $gradeClass)[1];
                } else {
                    $classes = Squad::whereGradeId($id)
                        ->where('enabled', 1)
                        ->pluck('name', 'id');
                    $students = Student::whereClassId($classes->keys()->first())
                        ->where('enabled', 1)
                        ->pluck('student_number', 'id');
                }
                break;
            case 'class':
                $list = Student::whereClassId($id)
                    ->where('enabled', 1)
                    ->get();
                if (!empty($list)) {
                    foreach ($list as $s) {
                        $students[$s->id] = $s->user->realname . "-" . $s->student_number;
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
     * 根据年级获取对应的班级和对应的学生
     * @param $gradeId
     * @param $gradeClass
     * @return array
     */
    function getClass($gradeId, $gradeClass) {
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
                        ->pluck('student_number', 'id');
                    break;
                }
            }
        }
        
        return [$classes, $students];
    }
    
}
