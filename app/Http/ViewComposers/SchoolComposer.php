<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
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
                'titles' => ['#', '名称', '地址', '类型', '公众号', '创建于', '更新于', '状态 . 操作'],
                'batch'  => true,
            ];
        } else {
            $params = [
                'schoolTypes' => SchoolType::whereEnabled(1)
                    ->pluck('name', 'id')->toArray(),
                'apps'        => [null => '[所属公众号]'] + App::where('token', '<>', null)
                    ->pluck('name', 'id')->toArray(),
                'corpId'      => (new Corp)->corpId(),
                'uris'        => $this->uris(),
                'apis'        => User::where(['group_id' => Group::whereName('api')->first()->id, 'enabled' => 1])
                    ->pluck('realname', 'id')->toArray(),
                'selectedApis' => null,
                'disabled'    => null,   # disabled - 是否显示'返回列表'和'取消'按钮
            ];
            if (Request::route('id')) {
                $school = School::find(Request::route('id'));
                if ($school->user_ids) {
                    $params['selectedApis'] = User::whereIn('id', explode(',', $school->user_ids))
                        ->pluck('realname', 'id')->toArray();
                }
            }
            $data = $params;
        }
    
        $view->with($data);
        
    }
    
}