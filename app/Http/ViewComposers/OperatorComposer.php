<?php
namespace App\Http\ViewComposers;

use App\Models\Company;
use App\Models\Group;
use App\Models\School;
use Illuminate\Contracts\View\View;

class OperatorComposer {
    
    protected $company, $school, $group;
    
    public function __construct(Company $company, School $school, Group $group) {
        
        $this->company = $company;
        $this->school = $school;
        $this->group = $group;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'companies' => $this->company->pluck('name', 'id'),
            'schools'   => $this->school->pluck('name', 'id'),
            'groups'    => $this->group->pluck('name', 'id'),
        ]);
        
    }
    
}