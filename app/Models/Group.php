<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $name 角色名称
 * @property string $remark 角色备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereEnabled($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereRemark($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @method static Builder|Group whereSchoolId($value)
 * @mixin \Eloquent
 * @property-read Collection|User[] $users
 * @property-read Collection|Menu[] $menus
 * @property-read Collection|Tab[] $tabs
 * @property-read Collection|Action[] $actions
 * @property int|null $school_id
 * @property-read \App\Models\GroupType $groupType
 * @property-read \App\Models\School|null $school
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
    public function users() { return $this->hasMany('App\Models\User'); }

    /**
     * 返回指定角色所属的学校对象
     *
     * @return BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取对象的菜单对象
     *
     * @return BelongsToMany
     */
    public function menus() { return $this->belongsToMany('App\Models\Menu', 'groups_menus'); }
    
    /**
     * 获取对象的功能对象
     *
     * @return BelongsToMany
     */
    public function actions() { return $this->belongsToMany('App\Models\Action', 'actions_groups'); }
    
    /**
     * 获取对应的卡片对象
     *
     * @return BelongsToMany
     */
    public function tabs() { return $this->belongsToMany('App\Models\Tab', 'groups_tabs'); }
    
    /**
     * 返回所属的角色类型对象
     *
     * @return BelongsTo
     */
    public function groupType() { return $this->belongsTo('App\Models\GroupType'); }

    /**
     * 保存角色
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function store(array $data) {

        try {
            DB::transaction(function () use ($data) {
                $groupData = [
                    'name' => $data['name'],
                    'remark' => $data['remark'],
                    'enabled' => $data['enabled'],
                    'school_id' => $data['school_id'],
                ];
                $g = $this->create($groupData);
                # 功能与角色的对应关系
                $actionIds = $data['acitonId'];
                $actionGroup = new ActionGroup();
                $actionGroup->storeByGroupId($g->id, $actionIds);
                # 功能与菜单的对应关系
                $menuIds = explode(',', $data['menu_ids']);
                $groupMenu = new GroupMenu();
                $groupMenu->storeByGroupId($g->id, $menuIds);
                # 功能与卡片的对应关系
                $tabIds = $data['tabId'];
                $groupTab = new GroupTab();
                $groupTab->storeByGroupId($g->id, $tabIds);

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
     */
    public function modify(array $data, $id) {

        $group = $this->find($id);
        if (!$group) {
            return false;
        }
        try {
            DB::transaction(function () use ($data, $group, $id) {

                $groupData = [
                    'name' => $data['name'],
                    'remark' => $data['remark'],
                    'enabled' => $data['enabled'],
                ];
                $group->update($groupData);
                # 功能与角色的对应关系
                $actionIds = $data['acitonId'];
                $actionGroup = new ActionGroup();
                $actionGroup->storeByGroupId($id, $actionIds);
                # 功能与菜单的对应关系
                $menuIds = explode(',', $data['menu_ids']);
                $groupMenu = new GroupMenu();
                $groupMenu->storeByGroupId($id, $menuIds);
                # 功能与卡片的对应关系
                $tabIds = $data['tabId'];
                $groupTab = new GroupTab();
                $groupTab->storeByGroupId($id, $tabIds);

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
    public function remove($id) {

        $group = $this->find($id);
        if (!$group) { return false; }
        
        return $this->removable($group) ? $group->delete() : false;

    }

    public function datatable() {

        $columns = [
            ['db' => 'Groups.id', 'dt' => 0],
            [
                'db' => 'Groups.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return '<i class="fa fa-meh-o"></i>&nbsp;' . $d;
                }
            ],
            [
                'db' => 'School.name as schoolname', 'dt' => 2,
                'formatter' => function ($d) {
                    return '<i class="fa fa-university"></i>&nbsp;' . $d;
                }
            ],
            ['db' => 'Groups.remark', 'dt' => 3],
            ['db' => 'Groups.created_at', 'dt' => 4],
            ['db' => 'Groups.updated_at', 'dt' => 5],
            [
                'db' => 'Groups.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Groups.school_id'
                ]
            ]
        ];
        $condition = '';
        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
                break;
            case '企业':
                $corpId = Corp::whereDepartmentId($user->topDeptId($user))
                    ->first()->id;
                $joins[] = [
                    'table' => 'corps',
                    'alias' => 'Corp',
                    'type' => 'INNER',
                    'conditions' => [
                        'Corp.id = School.corp_id'
                    ]
                ];
                $condition = 'Corp.id = ' . $corpId;
                break;
            case '学校':
                $schoolId = School::whereDepartmentId($user->topDeptId($user))
                    ->first()->id;
                $condition = 'School.id = ' . $schoolId;
                break;
        }
        if (empty($condition)) {
            return Datatable::simple($this, $columns, $joins);
        }
        return Datatable::simple($this, $columns, $joins, $condition);

    }

}
