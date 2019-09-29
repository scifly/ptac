<?php
namespace App\Http\ViewComposers;

use App\Models\Article;
use Illuminate\Contracts\View\View;

/**
 * Class ArticleComposer
 * @package App\Http\ViewComposers
 */
class ArticleComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new Article)->compose()
        );
        
    }
    
}