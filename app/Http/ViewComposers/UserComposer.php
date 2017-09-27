<?php
namespace App\Http\ViewComposers;

use App\Models\Group;
use Illuminate\Contracts\View\View;

class UserComposer {
    
    protected $groups;
    
    public function __construct(Group $groups) {
        
        $this->groups = $groups;
    }
    
    public function compose(View $view) {
        
        $view->with([
            'groups' => $this->groups->pluck('name', 'id'),
        ]);
    }
    
}