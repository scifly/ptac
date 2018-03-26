<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class CustodianIndexComposer {

    use ModelTrait;

    public function compose(View $view) {
    
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        reset($grades);
        $classes = Squad::whereGradeId(key($grades))
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
    
        $view->with([
            'grades' => $grades,
            'classes' => $classes,
            'title' => '导出监护人',
            'uris' => $this->uris()
        ]);

    }

}