<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\MessageType;
use App\Models\School;
use Illuminate\Contracts\View\View;

class MessageIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $school = School::find($this->schoolId());
        $data = App::whereEnabled(1)
            ->where('corp_id', $school->corp_id)
            ->get(['id', 'name', 'square_logo_url']);
        $apps = [];
        foreach ($data as $datum) {
            $apps[$datum['id']] = $datum['name'] . '|' . $datum['square_logo_url'];
        }
        $view->with([
            'titles'         => ['#', '通信方式', '应用', '消息批次', '接收者', '类型', '发送于', '状态(发送/阅读)'],
            'apps'           => $apps,
            'messageMaxSize' => env('MESSAGE_MAX_SIZE'),
            'messageTypes'   => MessageType::pluck('name', 'id')->toArray(),
            'uris'           => $this->uris(),
        ]);
    }
    
}