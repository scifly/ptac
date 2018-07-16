<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\WapSite;
use Illuminate\Contracts\View\View;

/**
 * Class WapSiteIndexComposer
 * @package App\Http\ViewComposers
 */
class WapSiteIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with([
            'ws' => WapSite::whereSchoolId($this->schoolId())->first()
        ]);
    
    }
    
}