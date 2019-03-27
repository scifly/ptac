<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class MajorComposer
 * @package App\Http\ViewComposers
 */
class MajorComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '名称', '备注', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $schoolId = $this->schoolId();
            $selectedSubjects = [];
            if (Request::route('id')) {
                $selectedSubjects = Major::find(Request::route('id'))
                    ->subjects->pluck('name', 'id')->toArray();
            }
            $data = [
                'subjects'         => Subject::whereSchoolId($schoolId)->pluck('name', 'id'),
                'selectedSubjects' => $selectedSubjects,
            ];
        }
        
        $view->with($data);
        
    }
    
}