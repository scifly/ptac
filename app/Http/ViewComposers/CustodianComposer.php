<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class CustodianComposer
 * @package App\Http\ViewComposers
 */
class CustodianComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
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
            ->get()->toArray();
        $students = [];
        foreach ($records as $record) {
            if (!isset($record['user'])) {
                continue;
            }
            $students[$record['id']] = $record['user']['realname'] . '-' . $record['card_number'];
        }
        $mobiles = $relations = [];
        if (Request::route('id') && Request::method() == 'GET') {
            $mobiles = Custodian::find(Request::route('id'))->user->mobiles;
            $relations = CustodianStudent::whereCustodianId(Request::route('id'))->get();
        }
        $view->with([
            'grades'       => $grades,
            'classes'      => $classes,
            'students'     => $students,
            'mobiles'      => $mobiles,
            'relations'    => $relations,
            'title'        => '新增监护关系',
            'relationship' => true,
        ]);
        
    }
    
}