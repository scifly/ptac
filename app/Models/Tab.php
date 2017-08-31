<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\TabRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\Tab
 *
 * @property int $id
 * @property string $name 卡片名称
 * @property string|null $remark 卡片备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Tab whereCreatedAt($value)
 * @method static Builder|Tab whereEnabled($value)
 * @method static Builder|Tab whereId($value)
 * @method static Builder|Tab whereName($value)
 * @method static Builder|Tab whereRemark($value)
 * @method static Builder|Tab whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Collection|Action[] $actions
 * @property-read Collection|Menu[] $menus
 * @property int|null $icon_id 图标ID
 * @method static Builder|Tab whereIconId($value)
 * @property-read \App\Models\Icon|null $icon
 * @property int $action_id 默认加载的Action ID
 * @property-read \App\Models\Action $action
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tab whereActionId($value)
 */
class Tab extends Model {
    
    protected $fillable = [
        'name', 'remark', 'icon_id', 'action_id', 'enabled'
    ];
    
    public function menus() {
        
        return $this->belongsToMany('App\Models\Menu', 'menus_tabs');
        
    }
    
    public function icon() {
        
        return $this->belongsTo('App\Models\Icon');
        
    }
    
    public function action() {
        
        return $this->belongsTo('App\Models\Action');
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Tab.id', 'dt' => 0],
            ['db' => 'Tab.name', 'dt' => 1],
            [
                'db' => 'Icon.name as iconname', 'dt' => 2,
                'formatter' => function ($d) {
                    return isset($d) ? '<i class="' . $d . '"></i>&nbsp;' . $d : '[n/a]';
                }
            ],
            ['db' => 'Tab.remark', 'dt' => 3],
            ['db' => 'Tab.created_at', 'dt' => 4],
            ['db' => 'Tab.updated_at', 'dt' => 5],
            [
                'db' => 'Tab.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'icons',
                'alias' => 'Icon',
                'type' => 'LEFT',
                'conditions' => [
                    'Icon.id = Tab.icon_id'
                ]
            
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
    public function store(TabRequest $request) {
        
        try {
            $exception = DB::transaction(function() use ($request) {
                $t = $this->create($request->all());
                $menuTab = new MenuTab();
                $menuIds = $request->input('menu_ids', []);
                $menuTab->storeByTabId($t->id, $menuIds);
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    public function modify(Request $request, $tabId) {
        
        $tab = $this->find($tabId);
        if (!isset($tab)) { return false; }
        try {
            $exception = DB::transaction(function() use($request, $tabId, $tab) {
                $tab->update($request->all());
                $menuIds = $request->input('menu_ids', []);
                $menuTab = new MenuTab();
                $menuTab::whereTabId($tabId)->delete();
                $menuTab->storeByTabId($tabId, $menuIds);
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    public function remove($tabId) {
        
        $tab = $this->find($tabId);
        if (!isset($tab)) { return false; }
        try {
            $exception = DB::transaction(function() use ($tabId, $tab) {
                # 删除指定的卡片记录
                $tab->delete();
                # 删除与指定卡片绑定的菜单记录
                MenuTab::whereTabId($tabId)->delete();
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
}
