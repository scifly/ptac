<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Company;
use App\Models\Corp;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class CorpComposer {
    
    use ModelTrait;
    
    protected $menu;
    
    function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    public function compose(View $view) {
        
        $companies = Company::pluck('name', 'id');
        if ($this->menu->menuId(session('menuId', '企业'))) {
            $disabled = null;
            if (Request::route('id')) {
                $corp = Corp::find(Request::route('id'));
                $companies = [$corp->company_id => $corp->company->name];
                $disabled = false;
            }
            $view->with([
                'companies' => $companies,
                'disabled' => $disabled,
                'uris' => $this->uris()
            ]);
        } else {
            $view->with([
                'companies' => $companies,
                'uris'      => $this->uris(),
            ]);
        }
        
    }
    
}