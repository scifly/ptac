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
        list($optionAll, $htmlCommType, $htmlApp, $htmlMessageType) = $this->messageFilters();
        $view->with([
            'titles'       => [
                '#',
                ['title' => '通信方式', 'html' => $htmlCommType],
                ['title' => '应用', 'html' => $htmlApp],
                '消息批次', '接收者',
                ['title' => '类型', 'html' => $htmlMessageType],
                ['title' => '发送于', 'html' => $this->inputDateTimeRange('发送于')],
                [
                    'title' => '状态',
                    'html' => $this->singleSelectList(
                        array_merge($optionAll, [0 => '未发', 1 => '已发']), 'filter_sent'
                    )
                ],
            ],
            'rTitles'      => [
                '#',
                ['title' => '通信方式', 'html' => $htmlCommType],
                ['title' => '应用', 'html' => $htmlApp],
                '消息批次', '发送者',
                ['title' => '类型', 'html' => $htmlMessageType],
                ['title' => '接收于', 'html' => $this->inputDateTimeRange('接收于')],
                [
                    'title' => '状态',
                    'html' => $this->singleSelectList(
                        array_merge($optionAll, [0 => '未读', 1 => '已读']), 'filter_read'
                    )
                ],
            ],
            'apps'         => $apps,
            'smsMaxLength' => 300,
            'messageTypes' => MessageType::pluck('name', 'id')->toArray(),
            'batch'        => true,
            'filter'       => true
        ]);
    }
    
}