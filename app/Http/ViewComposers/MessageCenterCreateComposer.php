<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\Grade;
use App\Models\MessageType;
use App\Models\School;
use App\Models\Squad;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class MessageCenterCreateComposer
 * @package App\Http\ViewComposers
 */
class MessageCenterCreateComposer {
    
    use ModelTrait;
    
    protected $grade, $squad;
    
    /**
     * MessageCenterCreateComposer constructor.
     * @param Grade $grade
     * @param Squad $squad
     */
    function __construct(Grade $grade, Squad $squad) {
        
        $this->grade = $grade;
        $this->squad = $squad;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $user = Auth::user();
        $view->with([
            'schoolDept'   => Department::find(School::find(session('schoolId'))->department_id),
            'departments'  => Department::whereIn('id', $this->departmentIds($user->id, session('schoolId')))->get(),
            'messageTypes' => MessageType::pluck('name', 'id'),
            'msgTypes'     => [
                'text'     => '文本',
                'image'    => '图片',
                'voice'    => '语音',
                'video'    => '视频',
                'file'     => '文件',
                'textcard' => '卡片',
                'mpnews'   => '图文',
                'sms'      => '短信',
            ],
            'users'        => [],
        ]);
        
    }
    
}