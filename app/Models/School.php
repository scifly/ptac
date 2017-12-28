<?php

namespace App\Models;

use App\Events\SchoolCreated;
use App\Events\SchoolDeleted;
use App\Events\SchoolUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
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
 * App\Models\School
 *
 * @property int $id
 * @property int $school_type_id 学校类型ID
 * @property string $name 学校名称
 * @property string $address 学校地址
 * @property float $longitude 学校所处经度
 * @property float $latitude 学校所处纬度
 * @property int $corp_id 学校所属企业ID
 * @property int $sms_max_cnt 学校短信配额
 * @property int $sms_used 短信已使用量
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|School whereAddress($value)
 * @method static Builder|School whereCorpId($value)
 * @method static Builder|School whereCreatedAt($value)
 * @method static Builder|School whereEnabled($value)
 * @method static Builder|School whereId($value)
 * @method static Builder|School whereLatitude($value)
 * @method static Builder|School whereLongitude($value)
 * @method static Builder|School whereName($value)
 * @method static Builder|School whereSchoolTypeId($value)
 * @method static Builder|School whereSmsMaxCnt($value)
 * @method static Builder|School whereSmsUsed($value)
 * @method static Builder|School whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Corp $corp
 * @property-read Collection|Educator[] $educator
 * @property-read Collection|Grade[] $grade
 * @property-read SchoolType $schoolType
 * @property-read Collection|Semester[] $semesters
 * @property-read Collection|Subject[] $subject
 * @property-read WapSite $wapsite
 * @property-read WapSite $wapSite
 * @property-read Collection|AttendanceMachine[] $attendanceMachines
 * @property-read Collection|Squad[] $classes
 * @property-read Collection|ConferenceRoom[] $conferenceRooms
 * @property-read Collection|Department[] $departments
 * @property-read Collection|Educator[] $educators
 * @property-read Collection|Grade[] $grades
 * @property-read Collection|Major[] $majors
 * @property-read Collection|Menu[] $menus
 * @property-read Collection|PollQuestionnaire[] $pollQuestionnaires
 * @property-read Collection|Procedure[] $procedures
 * @property-read Collection|Subject[] $subjects
 * @property-read Collection|Team[] $teams
 * @property-read Collection|WapSiteModule[] $wapSiteModules
 * @property int $department_id 对应的部门ID
 * @property-read \App\Models\Department $department
 * @method static Builder|School whereDepartmentId($value)
 * @property int $menu_id 对应的菜单ID
 * @property-read \App\Models\Menu $menu
 * @method static Builder|School whereMenuId($value)
 * @property-read Collection|ExamType[] $examTypes
 * @property-read Collection|Group[] $groups
 */
class School extends Model {

    use ModelTrait;

    protected $fillable = [
        'name', 'address', 'school_type_id', 'menu_id',
        'corp_id', 'department_id', 'enabled',
    ];

    /**
     * 返回对应的部门对象
     *
     * @return BelongsTo
     */
    public function department() { return $this->belongsTo('App\Models\Department'); }

    /**
     * 返回对应的菜单对象
     *
     * @return BelongsTo
     */
    public function menu() { return $this->belongsTo('App\Models\Menu'); }

    /**
     * 返回所属学校类型对象
     *
     * @return BelongsTo
     */
    public function schoolType() { return $this->belongsTo('App\Models\SchoolType'); }

    /**
     * 返回所属企业对象
     *
     * @return BelongsTo
     */
    public function corp() { return $this->belongsTo('App\Models\Corp'); }

    /**
     * 获取隶属指定学校的所有角色对象
     *
     * @return HasMany
     */
    public function groups() { return $this->hasMany('App\Models\Group'); }

    /**
     * 获取指定学校所有的考勤机对象
     *
     * @return HasMany
     */
    public function attendanceMachines() { return $this->hasMany('App\Models\AttendanceMachine'); }

    /**
     * 获取所有的会议室对象
     *
     * @return HasMany
     */
    public function conferenceRooms() { return $this->hasMany('App\Models\ConferenceRoom'); }

    /**
     * 获取指定学校的所有调查问卷对象
     *
     * @return HasMany
     */
    public function pollQuestionnaires() { return $this->hasMany('App\Models\PollQuestionnaire'); }

    /**
     * 获取指定学校的所有审批流程对象
     *
     * @return HasMany
     */
    public function procedures() { return $this->hasMany('App\Models\Procedure'); }

    /**
     * 获取指定学校所有的学期对象
     *
     * @return HasMany
     */
    public function semesters() { return $this->hasMany('App\Models\Semester'); }

    /**
     * 获取指定学校所有的科目对象
     *
     * @return HasMany
     */
    public function subjects() { return $this->hasMany('App\Models\Subject'); }

    /**
     * 获取指定学校的所有教职员工组对象
     *
     * @return HasMany
     */
    public function teams() { return $this->hasMany('App\Models\Team'); }

    /**
     * 获取指定学校所有的年级对象
     *
     * @return HasMany
     */
    public function grades() { return $this->hasMany('App\Models\Grade'); }

    /**
     * 获取指定学校的所有专业对象
     *
     * @return HasMany
     */
    public function majors() { return $this->hasMany('App\Models\Major'); }

    /**
     * 获取指定学校包含的所有考试类型对象
     *
     * @return HasMany
     */
    public function examTypes() { return $this->hasMany('App\Models\ExamType'); }

    /**
     * 获取指定学校所有的教职员工对象
     *
     * @return HasMany
     */
    public function educators() { return $this->hasMany('App\Models\Educator'); }

    /**
     * 获取指定学校的微网站对象
     *
     * @return HasOne
     */
    public function wapSite() { return $this->hasOne('App\Models\WapSite'); }

    /**
     * 通过WapSite中间对象获取所有的微网站模块对象
     *
     * @return HasManyThrough
     */
    public function wapSiteModules() {

        return $this->hasManyThrough('App\Models\WapSiteModule', 'App\Models\WapSite');

    }

    /**
     * 通过Grade中间对象获取所有的班级对象
     *
     * @return HasManyThrough
     */
    public function classes() {

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
    static function store(array $data, $fireEvent = false) {

        $school = self::create($data);
        if ($school && $fireEvent) {
            event(new SchoolCreated($school));
            return true;
        }

        return false;

    }

    /**
     * 更新学校
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    static function modify(array $data, $id, $fireEvent = false) {

        $school = self::find($id);
        $updated = $school->update($data);
        if ($updated && $fireEvent) {
            event(new SchoolUpdated(self::find($id)));
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
    static function remove($id, $fireEvent = false) {

        $school = self::find($id);
        if (!$school) { return false; }
        $removed = self::removable($school) ? $school->delete() : false;
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
    static function datatable() {

        $columns = [
            ['db' => 'School.id', 'dt' => 0],
            [
                'db' => 'School.name as schoolname', 'dt' => 1,
                'formatter' => function ($d) {
                    return '<i class="fa fa-university"></i>&nbsp;' . $d;
                },
            ],
            ['db' => 'School.address', 'dt' => 2],
            ['db' => 'SchoolType.name as typename', 'dt' => 3],
            [
                'db' => 'Corp.name as corpname', 'dt' => 4,
                'formatter' => function ($d) {
                    return '<i class="fa fa-weixin"></i>&nbsp;' . $d;
                },
            ],
            ['db' => 'School.created_at', 'dt' => 5],
            ['db' => 'School.updated_at', 'dt' => 6],
            [
                'db' => 'School.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'school_types',
                'alias' => 'SchoolType',
                'type' => 'INNER',
                'conditions' => [
                    'SchoolType.id = School.school_type_id',
                ],
            ],
            [
                'table' => 'corps',
                'alias' => 'Corp',
                'type' => 'INNER',
                'conditions' => [
                    'Corp.id = School.corp_id',
                ],
            ],
        ];

        return Datatable::simple(self::getModel(), $columns, $joins);

    }

    /**
     * 根据角色/菜单id获取school_id
     *
     * @return int|mixed
     */
    static function id() {

        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
            case '企业':
                $schoolMenuId = Menu::schoolMenuId(session('menuId'));
                $id = $schoolMenuId ? self::whereMenuId($schoolMenuId)->first()->id : 0;
                break;
            case '学校':
                $departmentId = $user->topDeptId();
                $id = School::whereDepartmentId($departmentId)->first()->id;
                break;
            default:
                $id = $user->educator->school_id;
                break;
        }

        return $id;

    }

}
