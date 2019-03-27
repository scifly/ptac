<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\Corp;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class AppComposer
 * @package App\Http\ViewComposers
 */
class AppComposer {
    
    use ModelTrait;
    
    protected $corp, $menu;
    
    /**
     * AppComposer constructor.
     * @param Corp $corp
     * @param Menu $menu
     */
    function __construct(Corp $corp, Menu $menu) {
        
        $this->corp = $corp;
        $this->menu = $menu;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $rootMenuId = $this->menu->menuId(session('menuId'), '企业');
            if (!$rootMenuId) {
                $corps = Corp::all()->pluck('name', 'id')->toArray();
            } else {
                $corp = Corp::whereMenuId($rootMenuId)->first();
                $corps = [$corp->id => $corp->name];
            }
            reset($corps);
            $apps = App::whereCorpId(key($corps))->get()->toArray();
            $this->formatDateTime($apps);
            $view->with([
                'corps' => $corps,
                'apps'  => $apps,
            ]);
        }
        
    }
    
    /**
     * @param $apps
     */
    private function formatDateTime(&$apps) {
        
        for ($i = 0; $i < sizeof($apps); $i++) {
            if ($apps[$i]['created_at']) {
                $apps[$i]['created_at'] = $this->humanDate($apps[$i]['created_at']);
            }
            if ($apps[$i]['updated_at']) {
                $apps[$i]['updated_at'] = $this->humanDate($apps[$i]['updated_at']);
            }
            
        }
        
    }
    
}