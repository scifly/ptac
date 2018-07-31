<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class MobileSiteModuleComposer
 * @package App\Http\ViewComposers
 */
class MobileSiteModuleComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'acronym' => session('acronym')
        ]);
        
    }
    
}