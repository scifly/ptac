<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class ScoreCenterComposer
 * @package App\Http\ViewComposers
 */
class ScoreCenterComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $user = Auth::user();
        $userid = json_decode($user->ent_attrs, true)['userid'];
        $view->with([
            'acronym' => session('acronym'),
            'userid'  => $userid
        ]);
        
    }
    
}