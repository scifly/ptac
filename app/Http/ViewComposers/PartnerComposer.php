<?php
namespace App\Http\ViewComposers;

use App\Models\User;
use Exception;
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
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            $this->partner->compose()
        );
        
    }
    
}