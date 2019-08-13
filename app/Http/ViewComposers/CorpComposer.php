<?php
namespace App\Http\ViewComposers;

use App\Models\Corp;
use Illuminate\Contracts\View\View;

/**
 * Class CorpComposer
 * @package App\Http\ViewComposers
 */
class CorpComposer {
    
    protected $corp;
    
    /**
     * CorpComposer constructor.
     * @param Corp $corp
     */
    function __construct(Corp $corp) {
        
        $this->corp = $corp;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            $this->corp->compose()
        );
        
    }
    
}