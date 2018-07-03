<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Menu;
use App\Models\School;
use Illuminate\Contracts\View\View;

/**
 * Class GroupCreateComposer
 * @package App\Http\ViewComposers
 */
class GroupCreateComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $menu = new Menu();
        $currentMenuId = session('menuId');
        if ($this->schoolId()) {
            $schools = School::find($this->schoolId())->pluck('name', 'id');
        } else if ($corpMenuId = $menu->menuId($currentMenuId, '企业')) {
            $corpId = Corp::whereMenuId($corpMenuId)->first()->id;
            $schools = School::whereCorpId($corpId)->where('enabled', 1)->pluck('name', 'id');
        } else {
            $schools = School::whereEnabled(1)->pluck('name', 'id');
        }
        $view->with([
            'schools' => $schools,
        ]);
        
    }
    
}