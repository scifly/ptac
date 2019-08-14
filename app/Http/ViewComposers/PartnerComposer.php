<?php
namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Contracts\View\View;

/**
 * Class OperatorIndexComposer
 * @package App\Http\ViewComposers
 */
class PartnerComposer {
    
    protected $partner;
    
    /**
     * PartnerComposer constructor.
     * @param User $partner
     */
    function __construct(User $partner) {
        
        $this->partner = $partner;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            $this->partner->compose()
        );
        
    }
    
}