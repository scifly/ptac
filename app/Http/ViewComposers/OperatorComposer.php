<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\User;
use Illuminate\Contracts\View\View;

/**
 * Class OperatorComposer
 * @package App\Http\ViewComposers
 */
class OperatorComposer {
    
    use ModelTrait;
    
    protected $user;
    
    /**
     * OperatorComposer constructor.
     * @param User $user
     */
    function __construct(User $user) {
        
        $this->user = $user;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        list($mobiles, $groups, $corps, $schools) = $this->user->compose();
        
        $view->with([
            'mobiles' => $mobiles,
            'groups'  => $groups,
            'corps'   => $corps,
            'schools' => $schools,
        ]);
        
    }
    
}