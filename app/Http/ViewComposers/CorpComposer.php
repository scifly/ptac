<?php
namespace App\Http\ViewComposers;

use App\Models\Company;
use Illuminate\Contracts\View\View;

class CorpComposer {
    
    protected $company;
    
    public function __construct(Company $company) { $this->company = $company; }
    
    public function compose(View $view) {
        
        $view->with(['companies' => $this->company->pluck('name', 'id')]);
        
    }
    
}