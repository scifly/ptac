<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, HttpStatusCode, JobTrait};
use App\Models\Card;
use App\Models\PassageLog;
use App\Models\Turnstile;
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB,
    Support\Facades\Log};
use Pusher\PusherException;
use Throwable;

/**
 * Class GatherPassageLog - 采集通行记录
 * @package App\Jobs
 */
class GatherPassageLog implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, JobTrait;
    
    protected $schoolId, $userId, $response;
    
    /**
     * GatherPassageLog constructor.
     *
     * @param integer|null $schoolId
     * @param integer|null $userId
     */
    function __construct($schoolId = null, $userId = null) {
        
        $this->schoolId = $schoolId;
        $this->userId = $userId;
        $this->response = [
            'userId'     => $userId,
            'title'      => __('messages.passage_log.title'),
            'statusCode' => HttpStatusCode::OK,
        ];
        
    }
    
    /**
     * 消息发送任务
     *
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
    
        try {
            DB::transaction(function () {
                $records = (new Turnstile)->invoke(
                    'getlogs',
                    $this->schoolId ? ['ids' => []] : ['ids' => [0]]
                );
                if ($this->schoolId) {
                    $fields = [
                        'school_id', 'user_id', 'category', 'direction', 'turnstile_id',
                        'door', 'clocked_at', 'created_at', 'updated_at', 'reason', 'status'
                    ];
                    $logs = [];
                    if (is_array($records)) {
                        foreach ($records as $record) {
                            $card = Card::whereSn($record['card_num'])->first();
                            $turnstile = Turnstile::whereSn($record['sn'])->first();
                            $createdAt = $updatedAt = now()->toDateTimeString();
                            $logs[] = array_combine($fields, [
                                $this->schoolId, $card ? $card->user_id : 0, $record['type'],
                                $record['direction'], $turnstile ? $turnstile->id : 0, $record['door_num'],
                                date('Y-m-d H:i:s', strtotime($record['time'])),
                                $createdAt, $updatedAt, $record['reason'], $record['valid']
                            ]);
                        }
                    }
                    $pl = new PassageLog;
                    foreach (array_chunk($logs, 200) as $chunk) {
                        $pl->insert($chunk);
                    }
                    $this->response['message'] = __('messages.passage_log.gathered');
                    (new Broadcaster)->broadcast($this->response);
                } else {
                    if (is_array($records)) {
                        $tpl = '您的孩子%s已于%s%s';
                        $smses = [];
                        foreach ($records as $record) {
                            if (!$card = Card::whereSn($record['card_num'])->first()) continue;
                            if ($card->user->group->name != '学生') continue;
                            foreach ($card->user->student->custodians as $custodian) {
                                $default = $custodian->user->mobiles->where('isdefault', 1)->first();
                                if (!$default) continue;
                                $smses[$default->mobile] = sprintf(
                                    $tpl,
                                    $card->user->realname,
                                    date('Y-m-d H:i:s', strtotime($record['time'])),
                                    $record['direction'] ? '进校' : '离校'
                                );
                            }
                        }
                        Log::info('smses', $smses);
                        foreach (array_chunk($smses, 200) as $chunk) {
                            foreach ($chunk as $mobile => $msg) {
                                // invoke sms sending api here
                            }
                        }
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
    /**
     * 任务异常处理
     *
     * @param Exception $exception
     * @throws PusherException
     */
    function failed(Exception $exception) {
        
        $this->eHandler($exception, $this->response);
        
    }
    
}
