<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Custodian;
use App\Models\Educator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class EducatorComposer
 * @package App\Http\ViewComposers
 */
class EducatorComposer {
    
    use ModelTrait;
    
    protected $educator, $custodian;
    
    /**
     * EducatorComposer constructor.
     * @param Educator $educator
     * @param Custodian $custodian
     */
    function __construct(Educator $educator, Custodian $custodian) {
        
        $this->educator = $educator;
        $this->custodian = $custodian;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        if (Request::route('id')) {
            $educator = $this->educator->find(Request::route('id'));
            if (!$educator->singular) {
                $custodianId = $this->custodian->where('user_id', $educator->user_id)->first()->id;
            }
        }
        list($squads, $subjects, $groups, $departmentIds, $departments, $mobiles) = $this->educator->compose();
        list($title, $grades, $classes, $students, $relations) = $this->custodian->compose($custodianId ?? null);
        $firstOption = [0 => '(请选择)'];
        $view->with([
            'squads'                => $firstOption + $squads,
            'subjects'              => $firstOption + $subjects,
            'groups'                => $firstOption + $groups,
            'mobiles'               => $mobiles,
            'selectedDepartmentIds' => $departmentIds,
            'selectedDepartments'   => $departments,
            'grades'                => $grades,
            'classes'               => $classes,
            'students'              => $students,
            'relations'             => $relations,
            'relationship'          => true,
            'title'                 => $title
        ]);
        
    }
    
}