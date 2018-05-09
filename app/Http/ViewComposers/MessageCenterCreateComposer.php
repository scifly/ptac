<?php
namespace App\Http\ViewComposers;

use App\Models\App;
use App\Models\Grade;
use App\Models\Squad;
use App\Helpers\ModelTrait;
use App\Models\MessageType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class MessageCenterCreateComposer {
    
    use ModelTrait;
    
    protected $grade, $squad;
    
    function __construct(Grade $grade, Squad $squad) {
        
        $this->grade = $grade;
        $this->squad = $squad;
        
    }
    
    public function compose(View $view) {
        
        $user = Auth::user();
        $view->with([
            'gradeDepts'   => $this->grade->departments($user->id),
            'classDepts'   => $this->squad->departments($user->id),
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