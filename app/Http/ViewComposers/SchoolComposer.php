<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Menu;
use App\Models\School;
use App\Models\SchoolType;
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
        
        $corps = Corp::whereEnabled(1)->pluck('name', 'id');
        $schoolTypes = SchoolType::whereEnabled(1)->pluck('name', 'id');
        $params = [
            'schoolTypes' => $schoolTypes,
            'corps'       => $corps,
            'uris'        => $this->uris(),
            'disabled'    => null   # diaabled - 是否显示'返回列表'和'取消'按钮
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
        } else {
            $menuId = $this->menu->menuId(
                session('menuId'), '企业'
            );
            if ($menuId) {
                $corp = Corp::whereMenuId($menuId)->first();
                $params['corps'] = [$corp->id => $corp->name];
            }
        }
        $view->with($params);
        
    }
    
}