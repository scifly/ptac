<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;

class SemesterComposer {

    protected $schools;

    public function __construct(School $schools) {

        $this->schools = $schools;

    }

    public function compose(View $view) {

        // $view->with('schoolTypes', $this->schoolTypes->pluck('name', 'id'));
        $view->with([
            'schools' => $this->schools->pluck('name', 'id'),
        ]);
    }

}