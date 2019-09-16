<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * App\Models\Group 角色
 *
 * @property int $id
 * @property string $name 角色名称
 * @property int|null $school_id
 * @property string $remark 角色备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Action[] $actions
 * @property-read Collection|Menu[] $menus
 * @property-read School|null $school
 * @property-read Collection|Tab[] $tabs
 * @property-read Collection|User[] $users
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereEnabled($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereRemark($value)
 * @method static Builder|Group whereSchoolId($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group query()
 * @mixin Eloquent
 * @property-read int|null $actions_count
 * @property-read int|null $menus_count
 * @property-read int|null $tabs_count
 * @property-read int|null $users_count
 */
class Group extends Model {
    
    use ModelTrait;
    
    protected $table = 'groups';
    
    protected $fillable = ['name', 'school_id', 'remark', 'enabled'];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /**
     * 获取指定角色下的所有用户对象
     *
     * @return HasMany
     */
    function users() { return $this->hasMany('App\Models\User'); }
    
    /**
     * 返回指定角色所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定角色可以访问的菜单对象
     *
     * @return BelongsToMany
     */
    function menus() { return $this->belongsToMany('App\Models\Menu', 'group_menu'); }
    
    /**
     * 获取指定角色可以访问的功能对象
     *
     * @return BelongsToMany
     */
    function actions() { return $this->belongsToMany('App\Models\Action', 'action_group'); }
    
    /**
     * 获取指定角色可以访问的卡片对象
     *
     * @return BelongsToMany
     */
    function tabs() { return $this->belongsToMany('App\Models\Tab', 'group_tab'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 角色列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Groups.id', 'dt' => 0],
            [
                'db'        => 'Groups.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return $this->iconHtml('fa-meh-o') . $d;
                },
            ],
            [
                'db'        => 'School.name as schoolname', 'dt' => 2,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'school');
                },
            ],
            [
                'db'        => 'Corp.name as corpname', 'dt' => 3,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'corp');
                },
            ],
            ['db' => 'Groups.remark', 'dt' => 4],
            ['db' => 'Groups.created_at', 'dt' => 5],
            ['db' => 'Groups.updated_at', 'dt' => 6],
            [
                'db'        => 'Groups.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'Groups.school_id IS NOT NULL ';
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Groups.school_id',
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
        if ($id = $this->schoolId()) {
            $condition .= 'AND School.id = ' . $id;
        } elseif ($menuId = (new Menu)->menuId(session('menuId'), '企业')) {
            $condition .= 'AND Corp.id = ' . Corp::whereMenuId($menuId)->first()->id;
        }
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存角色
     *
     * @param array $data
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $group = $this->create($data);
                $this->bindings($group->id, $data);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新角色
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                $this->find($id)->update($data);
                $this->bindings($id, $data);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除角色
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge([
                    'Group', 'ActionGroup', 'GroupMenu', 'GroupTab', 'Tab'],
                    'group_id', 'purge', $id
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回composer所需的view数据
     *
     * @return array
     */
    function compose() {
    
        if (explode('/', Request::path())[1] == 'index') {
            $data = [
                'titles' => ['#', '名称', '所属学校', '所属企业', '备注', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            # 学校列表
            $where = ['enabled' => Constant::ENABLED];
            if ($id = $this->schoolId()) {
                $where['id'] = $id;
            } elseif ($menuId = (new Menu)->menuId(session('menuId'), '企业')) {
                $where['corp_id'] = Corp::whereMenuId($menuId)->first()->id;
            }
            $data['schools'] = School::where($where)->pluck('name', 'id');
            # 控制器 & 功能列表
            $sGId = Group::whereName('学校')->first()->id;
            $tabs = Tab::whereIn('group_id', [0, $sGId])->where('category', 0)->get();
            /** @var Tab $tab */
            foreach ($tabs as $tab) {
                $actionList = [];
                foreach ($tab->actions as $action) {
                    if (!in_array(trim($action->name), ['创建微网站', '保存微网站', '删除微网站'])) {
                        $actionList[] = [
                            'id'     => $action->id,
                            'name'   => $action->name,
                            'method' => $action->method,
                        ];
                    }
                }
                $tabActions[] = [
                    'tab'     => ['id' => $tab->id, 'name' => $tab->comment],
                    'actions' => $actionList,
                ];
            }
            $data['tabActions'] = $tabActions ?? [];
            # 选定的菜单、控制器以及功能id
            if ($group = Group::find(Request::route('id'))) {
                $data = array_merge(
                    $data, array_combine(
                        ['selectedMenuIds', 'selectedTabIds', 'selectedActionsIds'],
                        array_map(
                            function ($property) use ($group) {
                                return $group->{$property}->pluck('id')->toArray();
                            }, ['menus', 'tabs', 'actions']
                        )
                    )
                );
            }
        
        }
        
        return $data;
        
    }
    
    /**
     * 返回指定学校的菜单树
     *
     * @return JsonResponse
     */
    function menuTree() {
        
        $schoolId = Request::query('schoolId');
        $menuId = School::find($schoolId)->menu_id;
        
        return (new Menu)->schoolTree($menuId);
        
    }
    
    /**
     * 返回指定学校的角色列表
     *
     * @return array
     */
    function list() {
        
        return $this->where([
            'school_id' => $this->schoolId(),
            'enabled'   => 1,
        ])->pluck('name', 'id');
        
    }
    
    /**
     * 保存角色 & 功能/菜单/卡片绑定关系
     *
     * @param $id
     * @param array $data
     */
    private function bindings($id, array $data) {
        
        array_map(
            function ($class, $ids, $forward) use ($id, $data) {
                $this->retain($class, $id, $data[$ids], $forward);
            },
            ['ActionGroup', 'GroupMenu', 'GroupTab'],
            ['action_ids', 'menu_ids', 'tab_ids'],
            [false, true, true]
        );
        
    }
    
}
