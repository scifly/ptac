<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\{Custodian, Educator};
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class CustodianComposer
 * @package App\Http\ViewComposers
 */
class CustodianComposer {
    
    use ModelTrait;
    
    protected $custodian, $educator;
    
    /**
     * CustodianComposer constructor.
     * @param Custodian $custodian
     * @param Educator $educator
     */
    function __construct(Custodian $custodian, Educator $educator) {
        
        $this->custodian = $custodian;
        $this->educator = $educator;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        if (Request::route('id')) {
            $custodian = $this->custodian->find(Request::route('id'));
            if (!$custodian->singular) {
                $educatorId = $this->educator->where('user_id', $custodian->user_id)->first()->id;
            }
        }
        list($title, $grades, $classes, $students, $relations, $mobiles) = $this->custodian->compose();
        list($squads, $subjects) = $this->educator->compose($educatorId ?? null);
        $firstOption = [0 => '(请选择)'];
        
        $view->with([
            'grades'       => $grades,
            'classes'      => $classes,
            'students'     => $students,
            'mobiles'      => $mobiles,
            'relations'    => $relations,
            'squads'       => $firstOption + $squads,
            'subjects'     => $firstOption + $subjects,
            'title'        => $title,
            'relationship' => true,
        ]);
        
    }
    
}