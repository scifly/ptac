<?php
namespace App\Http\ViewComposers;

use App\Models\App;
use App\Models\Corp;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class AppIndexComposer {
    
    protected $corp, $menu;
    
    function __construct(Corp $corp, Menu $menu) {
        
        $this->corp = $corp;
        $this->menu = $menu;
        
    }
    
    public function compose(View $view) {
        
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
    
    private function formatDateTime(&$apps) {
        
        Carbon::setLocale('zh');
        for ($i = 0; $i < sizeof($apps); $i++) {
            if ($apps[$i]['created_at']) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $apps[$i]['created_at']);
                $apps[$i]['created_at'] = $dt->diffForhumans();
            }
            if ($apps[$i]['updated_at']) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $apps[$i]['updated_at']);
                $apps[$i]['updated_at'] = $dt->diffForhumans();
            }
            
        }
        
    }
    
}
