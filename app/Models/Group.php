<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

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
     * 保存角色
     *
     * @param array $data
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function store(array $data) {

        try {
            DB::transaction(function () use ($data) {
                $group = self::create([
                    'name' => $data['name'],
                    'remark' => $data['remark'],
                    'enabled' => $data['enabled'],
                    'school_id' => $data['school_id'],
                ]);
                (new ActionGroup)->storeByGroupId($group->id, $data['action_ids']);
                (new GroupMenu)->storeByGroupId($group->id, $data['menu_ids']);
                (new GroupTab)->storeByGroupId($group->id, $data['tab_ids']);
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
     * @throws Exception
     * @throws \Throwable
     */
    function modify(array $data, $id) {

        try {
            DB::transaction(function () use ($data, $id) {
                $this->find($id)->update([
                    'name' => $data['name'],
                    'remark' => $data['remark'],
                    'enabled' => $data['enabled'],
                ]);
                (new ActionGroup)->storeByGroupId($id, $data['action_ids']);
                (new GroupMenu)->storeByGroupId($id, $data['menu_ids']);
                (new GroupTab)->storeByGroupId($id, $data['tab_ids']);
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
     * @throws Exception
     */
    function remove($id = null) {

        return $this->del($this, $id);

    }
    
    /**
     * 删除指定角色的所有数据
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function() use ($id) {
                ActionGroup::whereGroupId($id)->delete();
                GroupMenu::whereGroupId($id)->delete();
                GroupTab::whereGroupId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回指定学校的菜单树
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function menuTree() {
    
        $schoolId = Request::query('schoolId');
        $menuId = School::find($schoolId)->menu_id;
        
        return (new Menu())->schoolTree($menuId);
        
    }
    
    /**
     * 角色列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'Groups.id', 'dt' => 0],
            [
                'db' => 'Groups.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-meh-o', '') . $d;
                }
            ],
            [
                'db' => 'School.name as schoolname', 'dt' => 2,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-university text-purple', '') .
                        '<span class="text-purple">' . $d . '</span>';
                }
            ],
            [
                'db' => 'Corp.name as corpname', 'dt' => 3,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-weixin text-green', '') .
                        '<span class="text-green">' . $d . '</span>';
                }
            ],
            ['db' => 'Groups.remark', 'dt' => 4],
            ['db' => 'Groups.created_at', 'dt' => 5],
            ['db' => 'Groups.updated_at', 'dt' => 6],
            [
                'db' => 'Groups.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $condition = 'Groups.school_id IS NOT NULL ';
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Groups.school_id'
                ]
            ],
            [
                'table' => 'corps',
                'alias' => 'Corp',
                'type' => 'INNER',
                'conditions' => [
                    'Corp.id = School.corp_id'
                ]
            ]
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

}
