<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use Carbon\Carbon;
use Eloquent;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\BelongsToMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, Request};
use Illuminate\View\View;
use Throwable;

/**
 * App\Models\Module - 应用模块
 *
 * @property int $id
 * @property string $name
 * @property string $remark
 * @property int|null $tab_id
 * @property int $school_id 应用模块所属的学校id
 * @property int|null $order 模块位置
 * @property int $media_id 模块图标媒体id
 * @property string|null $uri
 * @property int $isfree 是否为免费模块
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int|null $group_id 应用模块所属角色id
 * @property-read Media $media
 * @property-read School $school
 * @property-read Tab|null $tab
 * @property-read Group|null $group
 * @property-read Collection|Student[] $students
 * @method static Builder|Module whereCreatedAt($value)
 * @method static Builder|Module whereEnabled($value)
 * @method static Builder|Module whereId($value)
 * @method static Builder|Module whereIsfree($value)
 * @method static Builder|Module whereMediaId($value)
 * @method static Builder|Module whereName($value)
 * @method static Builder|Module whereOrder($value)
 * @method static Builder|Module whereRemark($value)
 * @method static Builder|Module whereSchoolId($value)
 * @method static Builder|Module whereTabId($value)
 * @method static Builder|Module whereUpdatedAt($value)
 * @method static Builder|Module whereUri($value)
 * @method static Builder|Module whereGroupId($value)
 * @method static Builder|Module newModelQuery()
 * @method static Builder|Module newQuery()
 * @method static Builder|Module query()
 * @mixin Eloquent
 */
class Module extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'remark', 'tab_id',
        'media_id', 'group_id', 'uri',
        'isfree', 'school_id', 'order',
        'enabled',
    ];
    
    /**
     * 返回模块所属的控制器对象
     *
     * @return BelongsTo
     */
    function tab() { return $this->belongsTo('App\Models\Tab'); }
    
    /**
     * 返回模块所属的媒体对象
     *
     * @return BelongsTo
     */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 返回指定模块所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 返回指定模块所属的角色对象
     *
     * @return BelongsTo
     */
    function group() { return $this->belongsTo('App\Models\Group'); }
    
    /**
     * 返回订阅了指定增值应用模块的所有学生对象
     *
     * @return BelongsToMany
     */
    function students() { return $this->belongsToMany('App\Models\Student', 'module_student'); }
    
    /**
     * 应用模块列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Module.id', 'dt' => 0],
            ['db' => 'Module.name', 'dt' => 1],
            [
                'db'        => 'School.name as schoolname', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::icon($d, 'school');
                },
            ],
            [
                'db'        => 'Tab.comment', 'dt' => 3,
                'formatter' => function ($d) {
                    return $d ?? '-';
                },
            ],
            [
                'db'        => 'Module.uri', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d ?? '-';
                },
            ],
            [
                'db'        => 'Module.group_id', 'dt' => 5,
                'formatter' => function ($d) {
                    return !empty($d) ? Group::find($d)->name : '公用';
                },
            ],
            [
                'db'        => 'Module.isfree', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d ? '基本' : '增值';
                },
            ],
            ['db' => 'Module.created_at', 'dt' => 7, 'dr' => true],
            ['db' => 'Module.updated_at', 'dt' => 8, 'dr' => true],
            ['db' => 'Module.order', 'dt' => 9],
            [
                'db'        => 'Module.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'medias',
                'alias'      => 'Media',
                'type'       => 'INNER',
                'conditions' => [
                    'Media.id = Module.media_id',
                ],
            ],
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Module.school_id',
                ],
            ],
            [
                'table'      => 'tabs',
                'alias'      => 'Tab',
                'type'       => 'INNER',
                'conditions' => [
                    'Tab.id = Module.tab_id',
                ],
            ],
        ];
        $condition = 'Module.school_id IN (' . join(',', $this->schoolIds()) . ')';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存应用模块
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新应用模块
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id = null) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * （批量）删除应用模块
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(['Module'], 'id', 'purge', $id);
        
    }
    
    /**
     * 上传应用模块图标
     *
     * @return JsonResponse
     */
    function import() {
        
        $file = Request::file('file');
        abort_if(
            empty($file),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.empty_file')
        );
        $uploadedFile = (new Media())->import(
            $file, __('messages.wap_site_module.title')
        );
        abort_if(
            !$uploadedFile,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        
        return response()->json($uploadedFile);
        
    }
    
    /**
     * 返回应用首页
     *
     * @return Factory|View
     */
    function wIndex() {
        
        $role = Auth::user()->role();
        $part = session('part');
        $schools = session('schools');
        $cGId = Group::whereName('监护人')->first()->id;
        $schoolId = session('schoolId');
        $modules = $this->orderBy('order')->where([
            'school_id' => $schoolId, 'enabled' => 1,
        ])->get()->filter(
            function (Module $module) use ($role, $part, $schoolId, $cGId) {
                $mGId = $module->group_id;
                if (in_array($role, Constant::SUPER_ROLES)) {
                    return $mGId != $cGId;
                } elseif ($role == '监护人' || (isset($part) && $part == 'custodian')) {
                    return in_array($mGId, [0, $cGId]);
                } else {
                    $gId = Group::where([
                        'enabled' => 1, 'school_id' => $schoolId, 'name' => $role,
                    ])->first()->id;
                    
                    return in_array($mGId, [0, $gId]);
                }
            }
        );
        foreach ($modules as &$module) {
            if ($module->tab_id) {
                $tab = Tab::find($module->tab_id);
                if ($tab && $tab->action_id) {
                    $module->uri = str_replace(
                        '{acronym}', session('acronym'),
                        Action::find($tab->action_id)->route
                    );
                }
            }
        }
        if ($schools && $part) {
            $choice = 'both';
        } elseif ($schools && !$part) {
            $choice = 'schools';
        } elseif (!$schools && $part) {
            $choice = 'part';
        }
        
        return view('wechat.wechat.index', [
            'modules' => $modules,
            'school'  => School::find($schoolId)->name,
            'role'    => !isset($part) ? null : ($part == 'educator' ? Auth::user()->group->name : '监护人'),
            'choice'  => $choice ?? null,
        ]);
        
    }
    
    /**
     * 返回对当前用户可见的学校列表
     *
     * @return Factory|View
     */
    function schools() {
        
        $user = Auth::user();
        $schoolIds = $user->schoolIds($user->id, session('corpId'));
        
        return view('wechat.schools', [
            'schools' => School::whereIn('id', $schoolIds)->pluck('name', 'id'),
            'appId'   => session('appId') ? '/' . session('appId') : '',
        ]);
        
    }
    
    /**
     * 返回创建/编辑view使用的数据
     *
     * @return array
     */
    function compose() {
    
        $where = ['enabled' => 1];
        Auth::user()->role() != '企业' ?: $where['corp_id'] = (new Corp)->corpId();
        $schools = School::where($where)->pluck('name', 'id');
        $tabs = Tab::where(['enabled' => 1, 'category' => 1])->pluck('comment', 'id');
        if (explode('/', Request::path())[1] == 'index') {
            $nil = collect([null => '全部']);
            $statuses = ['未启用', '已启用'];
            $groups = Group::whereIn('name', ['监护人', '教职员工'])->pluck('name', 'id');
            
            return [
                'titles' => [
                    '#', '名称',
                    [
                        'title' => '所属学校',
                        'html'  => $this->htmlSelect(
                            $nil->union($schools), 'filter_school'
                        ),
                    ],
                    [
                        'title' => '控制器',
                        'html'  => $this->htmlSelect(
                            $nil->union($tabs), 'filter_tab_id'
                        ),
                    ],
                    'uri',
                    [
                        'title' => '所属角色',
                        'html'  => $this->htmlSelect(
                            $nil->union(['公用'])->union($groups), 'filter_group_id'
                        ),
                    ],
                    [
                        'title' => '类型',
                        'html'  => $this->htmlSelect(
                            $nil->union(['基本', '增值']), 'filter_isfree'
                        ),
                    ],
                    ['title' => '创建于', 'html' => $this->htmlDTRange('创建于')],
                    ['title' => '更新于', 'html' => $this->htmlDTRange('更新于')],
                    '排序',
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->htmlSelect(
                            $nil->union($statuses), 'filter_enabled'
                        ),
                    ],
                ],
                'filter' => true,
            ];
        } else {
            $schools->sortKeys();
            $groups = $this->groupList($schools->keys()->first());
            $module = $this->find(Request::route('id'));
            
            return array_combine(
                ['schools', 'groups', 'tabs', 'media'],
                [
                    $schools, $groups,
                    collect([null => ''])->union($tabs),
                    $module ? $module->media : null,
                ]
            );
        }
        
    }
    
    /**
     * 返回指定学校的角色列表
     *
     * @param $schoolId
     * @param bool $html - 是否返回html字符串
     * @return array|string
     */
    function groupList($schoolId, $html = false) {
        
        $roles = ['运营', '企业', '学校', '监护人'];
        $groups = [0 => '公用'] +
            array_combine(
                array_map(function ($role) {
                    return Group::whereName($role)->first()->id;
                }, $roles), $roles
            ) +
            Group::where(['enabled' => 1, 'school_id' => $schoolId])
                ->pluck('name', 'id')->toArray();
        
        return $html ? $this->htmlSelect($groups, 'group_id') : $groups;
        
    }
    
}
