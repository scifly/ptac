<?php
namespace App\Http\ViewComposers;

use App\Models\WsmArticle;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;

/**
 * Class WsmArticleComposer
 * @package App\Http\ViewComposers
 */
class WsmArticleComposer {
    
    /**
     * @param View $view
     * @throws BindingResolutionException
     */
    public function compose(View $view) {
        
        $view->with(
            (new WsmArticle)->compose()
        );
        
    }
    
}