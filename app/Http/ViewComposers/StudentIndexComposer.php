<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class StudentIndexComposer {

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
            'importTemplate' => 'files/students.xlsx',
            'title' => '导出学籍',
            'uris' => $this->uris()
        ]);

    }

}