<?php

namespace App\Http\ViewComposers;

use App\Models\Company;
use Illuminate\Contracts\View\View;

class CorpComposer {

    protected $companies;

    public function __construct(Company $companies) {
        $this->companies = $companies;
    }

    public function compose(View $view) {

        // $view->with('schoolTypes', $this->schoolTypes->pluck('name', 'id'));

        $view->with([
            'companies' => $this->companies->pluck('name', 'id')
        ]);
    }

}