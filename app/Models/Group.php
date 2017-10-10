<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Http\Requests\GroupRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
 * @mixin \Eloquent
 * @property-read Collection|User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Action[] $actions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Menu[] $menus
 * @property-read \App\Models\School $school
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tab[] $tabs
 */
class Group extends Model {
    
    use ModelTrait;

    const DT_ON = '<span class="badge bg-green">%s</span>';
    const DT_OFF = '<span class="badge bg-gray">%s</span>';
    const DT_LINK_SHOW = <<<HTML
        <a id="%s" href="javascript:void(0)" class="btn btn-primary btn-icon btn-circle btn-xs"  data-toggle="modal">
            <i class="fa fa-eye"></i>
        </a>
HTML;

    const DT_SPACE = '&nbsp;';

    protected $table = 'groups';
    
    protected $fillable = [
        'name', 'school_id', 'remark', 'enabled',
    ];
    
    /**
     * 获取指定角色下的所有用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users() { return $this->hasMany('App\Models\User'); }
    
    /**
     * 返回指定角色所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    public function menus() { return $this->belongsToMany('App\Models\Menu', 'groups_menus'); }
    
    public function actions() { return $this->belongsToMany('App\Models\Action', 'actions_groups'); }
    
    public function tabs() { return $this->belongsToMany('App\Models\Tab', 'groups_tabs'); }

    public function groupType(){ return $this->belongsTo('App\Models\GroupType'); }
    /**
     * 保存角色
     *
     * @param GroupRequest $request
     * @return bool
     * @internal param array $data
     */
    public function store(array $data) {
        
        try {
            $exception = DB::transaction(function() use ($data) {

                $groupData = [
                    'name'    => $data['name'],
                    'remark'  => $data['remark'],
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
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 更新角色
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    public function modify(array $data, $id) {
        
        $group = $this->find($id);
        if (!$group) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($data, $group, $id) {
                
                $groupData = [
                    'name'    => $data['name'],
                    'remark'  => $data['remark'],
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
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 删除角色
     *
     * @param $id
     * @return bool
     */
    public function remove($id) {
        
        $group = $this->find($id);
        if (!$group) { return false; }
        return $this->removable($group) ? $group->delete() : false;
        
    }
    
    public function datatable(array $params = []) {
        
        $columns = [
            ['db' => 'Groups.id', 'dt' => 0],
            ['db' => 'Groups.name', 'dt' => 1],
            ['db' => 'Groups.remark', 'dt' => 2],
            ['db' => 'Groups.created_at', 'dt' => 3],
            ['db' => 'Groups.updated_at', 'dt' => 4],
            [
                'db'        => 'Groups.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
            if($row['id'] < 4){
                $id = $row['id'];
                $status = $d ? sprintf(self::DT_ON, '已启用') : sprintf(self::DT_OFF, '未启用');
                $showLink = sprintf(self::DT_LINK_SHOW, 'show_' . $id);

                return $status . self::DT_SPACE . $showLink . self::DT_SPACE ;
            }else{
                return Datatable::dtOps($this, $d, $row);
            }

                },
            ],
        ];
        
        return Datatable::simple($this, $columns, null, empty($params) ? null : $params);
        
    }
}
