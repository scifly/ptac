<?php
namespace App\Http\ViewComposers;

use App\Models\App;
use App\Models\Corp;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AppIndexComposer {
    
    public function __construct() { }
    
    public function compose(View $view) {
       
        $user = Auth::user();
        if ($user->group->name == '运营') {
            $corps = Corp::pluck('name', 'id')->toArray();
            reset($corps);
            $apps = App::whereCorpId(key($corps))->get();
            $view->with(['corps' => $corps, 'apps' => $apps]);
        } else {
            $corp = Corp::whereDepartmentId($user->topDeptId($user))->first();
            $apps = App::whereCorpId($corp->id)->get();
            $view->with(['corp' => $corp, 'apps' => $apps]);
        }
        
    }
}
