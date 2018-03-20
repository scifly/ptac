<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Group;
use App\Models\School;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustodianComposer {
    
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
        reset($classes);
        $records = Student::with('user:id,realname')
            ->where('class_id', key($classes))
            ->where('enabled', 1)
            ->get()
            ->toArray();
        $students = [];
        foreach ($records as $record) {
            if (!isset($record['user'])) { continue; }
            $students[$record['id']] = $record['user']['realname'] . '(' . $record['card_number'] . ')';
        }
        
        $view->with([
            'grades'   => $grades,
            'classes'  => $classes,
            'students' => $students,
            'title'    => '新增监护关系',
            'uris'     => $this->uris(),
            'relationship' => true
        ]);
        
    }
    
}