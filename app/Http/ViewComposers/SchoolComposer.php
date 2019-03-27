<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class SchoolComposer
 * @package App\Http\ViewComposers
 */
class SchoolComposer {
    
    use ModelTrait;
    
    protected $menu;
    
    /**
     * SchoolComposer constructor.
     * @param Menu $menu
     */
    function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '名称', '地址', '类型', '所属企业', '创建于', '更新于', '同步状态', '状态 . 操作'],
                'batch'  => true,
            ];
        } else {
    
            $corps = Corp::whereEnabled(1)->pluck('name', 'id');
            $schoolTypes = SchoolType::whereEnabled(1)->pluck('name', 'id');
            $apis = User::where([
                'group_id' => Group::whereName('api')->first()->id,
                'enabled' => 1
            ])->pluck('realname', 'id')->toArray();
            $params = [
                'schoolTypes' => $schoolTypes,
                'corps'       => $corps,
                'uris'        => $this->uris(),
                'disabled'    => null,   # disabled - 是否显示'返回列表'和'取消'按钮
                'apis'        => $apis,
                'selectedApis' => null
            ];
            if ($this->menu->menuId(session('menuId'))) {
                if (Request::route('id')) {
                    $school = School::find(Request::route('id'));
                    $params['corps'] = [
                        $school->corp_id => $school->corp->name,
                    ];
                    $params['schoolTypes'] = [
                        $school->school_type_id => $school->schoolType->name,
                    ];
                    $params['disabled'] = true;
                }
            } elseif ($menuId = $this->menu->menuId(session('menuId'), '企业')) {
                $corp = Corp::whereMenuId($menuId)->first();
                $params['corps'] = [$corp->id => $corp->name];
            }
            if (Request::route('id')) {
                $school = School::find(Request::route('id'));
                if ($school->user_ids) {
                    $params['selectedApis'] = User::whereIn('id', explode(',', $school->user_ids))
                        ->get()->pluck('realname', 'id')->toArray();
                }
            }
            $data = $params;
        }
    
        $view->with($data);
        
    }
    
}