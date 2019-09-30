<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Jobs\CreateSchool;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Form;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasManyThrough,
    Relations\HasOne};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Request};
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
 * @property int|null $app_id 所属公众号对应的应用id
 * @property int $corp_id 学校所属企业ID
 * @property int|null $sms_balance 短信余额
 * @property int|null $sms_used 短信已使用量
 * @property int|null $sms_len 短信计费长度
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $department_id 对应的部门ID
 * @property-read Collection|Turnstile[] $turnstiles
 * @property-read Collection|Squad[] $classes
 * @property-read Collection|SubjectModule[] $subjectModules
 * @property-read Corp $corp
 * @property-read Department $department
 * @property-read Collection|Educator[] $educators
 * @property-read Collection|ExamType[] $examTypes
 * @property-read Collection|Exam[] $exams
 * @property-read Collection|Grade[] $grades
 * @property-read Collection|Group[] $groups
 * @property-read Collection|Major[] $majors
 * @property-read Menu $menu
 * @property-read Collection|Poll[] $polls
 * @property-read Collection|FlowType[] $flowTypes
 * @property-read SchoolType $schoolType
 * @property-read Collection|Semester[] $semesters
 * @property-read Collection|Subject[] $subjects
 * @property-read Collection|Tag[] $tags
 * @property-read Wap $wap
 * @property-read Collection|Column[] $modules
 * @property-read Collection|PassageLog[] $passageLogs
 * @property-read Collection|PassageRule[] $passageRules
 * @property-read App|null $app
 * @property-read Collection|Building[] $buildings
 * @property-read int|null $buildings_count
 * @property-read int|null $classes_count
 * @property-read int|null $conference_rooms_count
 * @property-read App $corpApp
 * @property-read int|null $educators_count
 * @property-read int|null $exam_types_count
 * @property-read int|null $exams_count
 * @property-read int|null $grades_count
 * @property-read int|null $groups_count
 * @property-read int|null $majors_count
 * @property-read int|null $passage_logs_count
 * @property-read int|null $passage_rules_count
 * @property-read int|null $polls_count
 * @property-read Collection|Room[] $rooms
 * @property-read int|null $rooms_count
 * @property-read int|null $semesters_count
 * @property-read int|null $subject_modules_count
 * @property-read int|null $subjects_count
 * @property-read int|null $tags_count
 * @property-read int|null $turnstiles_count
 * @property-read int|null $modules_count
 * @property-read int|null $flow_types_count
 * @method static Builder|School whereAddress($value)
 * @method static Builder|School whereCorpId($value)
 * @method static Builder|School whereCreatedAt($value)
 * @method static Builder|School whereDepartmentId($value)
 * @method static Builder|School whereEnabled($value)
 * @method static Builder|School whereId($value)
 * @method static Builder|School whereAppId($value)
 * @method static Builder|School whereLatitude($value)
 * @method static Builder|School whereLongitude($value)
 * @method static Builder|School whereMenuId($value)
 * @method static Builder|School whereName($value)
 * @method static Builder|School whereSchoolTypeId($value)
 * @method static Builder|School whereSignature($value)
 * @method static Builder|School whereSmsBalance($value)
 * @method static Builder|School whereSmsUsed($value)
 * @method static Builder|School whereSmsLen($value)
 * @method static Builder|School whereUpdatedAt($value)
 * @method static Builder|School whereUserIds($value)
 * @method static Builder|School newModelQuery()
 * @method static Builder|School newQuery()
 * @method static Builder|School query()
 * @mixin Eloquent
 * @property-read Collection|ComboType[] $comboTypes
 * @property-read int|null $combo_types_count
 * @property-read Collection|Prize[] $prizes
 * @property-read int|null $prizes_count
 * @property-read Collection|Column[] $columns
 * @property-read int|null $columns_count
 */
class School extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'address', 'school_type_id',
        'menu_id', 'signature', 'corp_id',
        'longitude', 'latitude', 'department_id',
        'user_ids', 'app_id', 'sms_balance',
        'sms_used', 'sms_len', 'enabled',
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function department() { return $this->belongsTo('App\Models\Department'); }
    
    /** @return BelongsTo */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /** @return BelongsTo */
    function schoolType() { return $this->belongsTo('App\Models\SchoolType'); }
    
    /** @return BelongsTo */
    function corp() { return $this->belongsTo('App\Models\Corp'); }
    
    /** @return BelongsTo */
    function corpApp() { return $this->belongsTo('App\Models\App'); }
    
    /** @return HasMany */
    function groups() { return $this->hasMany('App\Models\Group'); }
    
    /** @return HasMany */
    function turnstiles() { return $this->hasMany('App\Models\Turnstile'); }
    
    /** @return HasMany */
    function buildings() { return $this->hasMany('App\Models\Building'); }
    
    /** @return HasManyThrough */
    function rooms() { return $this->hasManyThrough('App\Models\Room', 'App\Models\Building'); }
    
    /** @return HasMany */
    function polls() { return $this->hasMany('App\Models\Poll'); }
    
    /** @return HasMany */
    function flowTypes() { return $this->hasMany('App\Models\FlowType'); }
    
    /** @return HasMany */
    function prizes() { return $this->hasMany('App\Models\Prize'); }
    
    /** @return HasMany */
    function comboTypes() { return $this->hasMany('App\Models\ComboType'); }
    
    /** @return HasMany */
    function semesters() { return $this->hasMany('App\Models\Semester'); }
    
    /** @return HasMany */
    function subjects() { return $this->hasMany('App\Models\Subject'); }
    
    /** @return HasMany */
    function tags() { return $this->hasMany('App\Models\Tag'); }
    
    /** @return HasMany */
    function grades() { return $this->hasMany('App\Models\Grade'); }
    
    /** @return HasMany */
    function majors() { return $this->hasMany('App\Models\Major'); }
    
    /** @return HasMany */
    function examTypes() { return $this->hasMany('App\Models\ExamType'); }
    
    /** @return HasManyThrough */
    function exams() { return $this->hasManyThrough('App\Models\Exam', 'App\Models\ExamType'); }
    
    /** @return HasMany */
    function educators() { return $this->hasMany('App\Models\Educator'); }
    
    /** @return HasOne */
    function wap() { return $this->hasOne('App\Models\Wap'); }
    
    /** @return HasManyThrough */
    function columns() { return $this->hasManyThrough('App\Models\Column', 'App\Models\Wap'); }
    
    /** @return HasManyThrough */
    function classes() {
        
        return $this->hasManyThrough(
            'App\Models\Squad', 'App\Models\Grade',
            'school_id', 'grade_id'
        );
        
    }
    
    /** @return HasMany */
    function passageLogs() { return $this->hasMany('App\Models\PassageLog'); }
    
    /** @return HasMany */
    function passageRules() { return $this->hasMany('App\Models\PassageRule'); }
    
    /** @return HasManyThrough */
    function subjectModules() {
        
        return $this->hasManyThrough(
            'App\Models\SubjectModule', 'App\Models\Subject',
            'school_id', 'subject_id'
        );
        
    }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
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
                    return $this->iconHtml($d, 'school');
                },
            ],
            ['db' => 'School.address', 'dt' => 2],
            ['db' => 'SchoolType.name as typename', 'dt' => 3],
            [
                'db'        => 'App.name as appname', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d ?? 'n/a';
                },
            ],
            ['db' => 'School.created_at', 'dt' => 5],
            ['db' => 'School.updated_at', 'dt' => 6],
            [
                'db'        => 'School.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $link = $this->anchor(
                        'recharge_' . $row['id'],
                        '短信充值 & 查询',
                        'fa-money'
                    );
                    
                    return Datatable::status($d, $row, false) .
                        (Auth::user()->can('act', (new Action)->uris()['recharge']) ? $link : '');
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
                'table'      => 'apps',
                'alias'      => 'App',
                'type'       => 'LEFT',
                'conditions' => [
                    'App.id = School.app_id',
                ],
            ],
        ];
        
        return Datatable::simple(
            $this, $columns, $joins, 'School.corp_id = ' . $this->corpId()
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
                    throw_if(
                        $corpChanged && !$this->removable($school),
                        new Exception(__('messages.school.corp_changed'))
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
     * 短信充值
     *
     * @param $id
     * @param array $data
     * @return JsonResponse
     * @throws Throwable
     */
    function recharge($id, array $data) {
        
        return (new SmsCharge)->recharge($this, $id, $data);
        
    }
    
    /**
     * 删除学校
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'purge.school_id' => [
                'Educator', 'ComboType', 'ExamType', 'Grade',
                'Group', 'Major', 'Module', 'Poll', 'FlowType',
                'Semester', 'Subject', 'Tag', 'Wap'
            ]
        ]);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * @return array
     * @throws Throwable
     */
    function compose() {
        
        switch (explode('/', Request::path())[1]) {
            case 'index':
                return [
                    'titles' => [
                        '#', '名称', '地址', '类型', '公众号',
                        '创建于', '更新于', '状态 . 操作',
                    ],
                    'batch'  => true,
                ];
            case 'create':
            case 'edit':
                $apps = App::where('category', 2)->pluck('name', 'id');
                $school = School::find(Request::route('id'));
                $where = ['group_id' => Group::whereName('api')->first()->id, 'enabled' => 1];
                !$school ?: $selectedApis = User::whereIn('id', explode(',', $school->user_ids))
                    ->pluck('realname', 'id');
                
                return [
                        'schoolTypes'  => SchoolType::whereEnabled(1)->pluck('name', 'id'),
                        'apps'         => collect([null => '[所属公众号]'])->union($apps),
                        'corpId'       => $this->corpId(),
                        'uris'         => (new Action)->uris(),
                        'apis'         => User::where($where)->pluck('realname', 'id'),
                        'selectedApis' => $selectedApis ?? collect([]),
                        'disabled'     => null,   # disabled - 是否显示'返回列表'和'取消'按钮
                ];
            default:
                return (new Message)->compose('recharge');
        }
        
    }
    
    /**
     * 获取字段列表
     *
     * @param $field
     * @param $id
     * @param null $gClasses
     * @return array
     */
    function fieldLists($field, $id, $gClasses = null) {
        
        # todo - fetch field list by role
        if ($field == 'grade') {
            if (isset($gClasses)) {
                [$classes, $students] = $this->getClass($id, $gClasses);
            } else {
                $classes = Squad::where([
                    'grade_id' => $id, 'enabled' => 1,
                ])->pluck('name', 'id');
                $students = Student::where([
                    'class_id' => $classes->keys()->first(),
                    'enabled'  => 1,
                ])->pluck('sn', 'id');
            }
        } else {
            $list = Student::where(['class_id' => $id, 'enabled' => 1])->get();
            if ($list->isNotEmpty()) {
                foreach ($list as $s) {
                    $students[$s->id] = $s->user->realname . "-" . $s->sn;
                }
            }
        }
        $ids = $names = ['gradeId', 'classId', 'studentId'];
        
        return array_combine(
            ['grades', 'classes', 'students'],
            array_map(
                function ($items, $id, $name) {
                    return Form::select($name, $items, null, [
                        'id' => $id, 'class' => 'form-control col-sm-6',
                    ])->toHtml();
                    
                }, [$grades ?? [], $classes ?? [], $students ?? []], $ids, $names
            )
        );
        
    }
    
    /**
     * 获取指定年级包含的的班级和学生列表
     *
     * @param $gradeId
     * @param $gradeClass
     * @return array
     */
    private function getClass($gradeId, $gradeClass): array {
        
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
