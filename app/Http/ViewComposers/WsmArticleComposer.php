<?php
namespace App\Http\ViewComposers;

use App\Models\WapSiteModule;
use Illuminate\Contracts\View\View;

class WsmArticleComposer {
    
    protected $wsms;
    
    public function __construct(WapSiteModule $wsms) {
        
        $this->wsms = $wsms;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        $view->with([
            'wsms' => $this->wsms->pluck('name', 'id'),
        ]);
    }
    
}