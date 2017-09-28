<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;

class ComboTypeComposer {

    protected $school;

    public function __construct(School $school) {

        $this->school = $school;

    }

    public function compose(View $view) {

        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
        ]);

    }

}