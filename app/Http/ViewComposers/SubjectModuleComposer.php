<?php
namespace App\Http\ViewComposers;

use App\Models\Subject;
use Illuminate\Contracts\View\View;


class SubjectModuleComposer {

    protected $subject;

    public function __construct(Subject $subject) { $this->subject = $subject; }

    public function compose(View $view) {

        $view->with([
            'subjects' => $this->subject->pluck('name', 'id'),
        ]);
        
    }

}