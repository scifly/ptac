<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 */
class Group extends Model {
    
    use ModelTrait;
    
    protected $table = 'groups';
    
    protected $fillable = [
        'name', 'school_id', 'remark', 'enabled',
    ];
    
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
    function menus() { return $this->belongsToMany('App\Models\Menu', 'groups_menus'); }
    
    /**
     * 获取指定角色可以访问的功能对象
     *
     * @return BelongsToMany
     */
    function actions() { return $this->belongsToMany('App\Models\Action', 'actions_groups'); }
    
    /**
     * 获取指定角色可以访问的卡片对象
     *
     * @return BelongsToMany
     */
    function tabs() { return $this->belongsToMany('App\Models\Tab', 'groups_tabs'); }
    
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
                    return sprintf(Snippet::ICON, 'fa-meh-o', '') . $d;
                },
            ],
            [
                'db'        => 'School.name as schoolname', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::icon($d, 'school');
                },
            ],
            [
                'db'        => 'Corp.name as corpname', 'dt' => 3,
                'formatter' => function ($d) {
                    return Snippet::icon($d, 'corp');
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
        $menu = new Menu();
        $currentMenuId = session('menuId');
        if ($this->schoolId()) {
            $condition .= 'AND School.id = ' . $this->schoolId();
        } else if ($corpMenuId = $menu->menuId($currentMenuId, '企业')) {
            $corpId = Corp::whereMenuId($corpMenuId)->first()->id;
            $condition .= 'AND Corp.id = ' . $corpId;
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
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
                $group = self::create([
                    'name'      => $data['name'],
                    'remark'    => $data['remark'],
                    'enabled'   => $data['enabled'],
                    'school_id' => $data['school_id'],
                ]);
                $this->binding($group->id, $data);
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
                $this->find($id)->update([
                    'name'    => $data['name'],
                    'remark'  => $data['remark'],
                    'enabled' => $data['enabled'],
                ]);
                $this->binding($id, $data);
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
    function groupList() {
        
        return $this->where([
            'school_id' => $this->schoolId(),
            'enabled'   => 1,
        ])->pluck('name', 'id')->toArray();
        
    }
    
    /**
     * 保存角色 & 功能/菜单/卡片绑定关系
     *
     * @param $id
     * @param array $data
     */
    private function binding($id, array $data) {
    
        array_map(
            function ($class, $ids, $forward) use ($id, $data) {
                $this->retain($class, $id, $data[$ids], $forward);
            }, [
                'ActionGroup', 'GroupMenu', 'GroupTab'
            ], [
                'action_ids', 'menu_ids', 'tab_ids'
            ], [
                false, true, true
            ]
        );
        
    }
    
}
