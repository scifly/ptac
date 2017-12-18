<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Company;
use Illuminate\Contracts\View\View;

class CorpComposer {
    use ControllerTrait;
    protected $company;

    public function __construct(Company $company) { $this->company = $company; }

    public function compose(View $view) {

        $view->with([
            'companies' => $this->company->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);

    }

}