<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\Icon;
use Illuminate\Contracts\View\View;

class TabComposer {
    
    use ModelTrait;
    
    protected $icon, $action;
    
    function __construct(Icon $icon, Action $action) {
        
        $this->icon = $icon;
        $this->action = $action;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'icons'   => $this->icon->icons(),
            'actions' => $this->action->actions(),
            'groups'  => [
                0 => '所有',
                1 => '运营',
                2 => '企业',
                3 => '学校',
            ],
            'uris'    => $this->uris(),
        ]);
        
    }
    
}