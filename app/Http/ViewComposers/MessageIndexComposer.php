<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\MessageType;
use App\Models\School;
use Illuminate\Contracts\View\View;

/**
 * Class MessageIndexComposer
 * @package App\Http\ViewComposers
 */
class MessageIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $school = School::find($this->schoolId());
        $data = App::whereEnabled(1)
            ->where('corp_id', $school->corp_id)
            ->whereIn('name', ['布置作业', '消息中心'])
            ->get(['id', 'name', 'square_logo_url']);
        $apps = [];
        foreach ($data as $datum) {
            $apps[$datum['id']] = $datum['name'] . '|' . $datum['square_logo_url'];
        }
        $view->with([
            'titles'       => ['#', '通信方式', '应用', '消息批次', '接收者', '类型', '发送于', '状态'],
            'rTitles'      => ['#', '通信方式', '应用', '消息批次', '发送者', '类型', '发送于', '状态'],
            'apps'         => $apps,
            'smsMaxLength' => 300,
            'messageTypes' => MessageType::pluck('name', 'id')->toArray(),
            'batch'        => true,
            'enable'       => '标记已发',
            'disable'      => '标记未发'
        ]);
    }
    
}