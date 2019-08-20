<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, HttpStatusCode, JobTrait};
use App\Models\Message;
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Arr,
    Support\Facades\DB};
use Pusher\PusherException;
use Throwable;

/**
 * Class SendMessage - 发送消息
 * @package App\Jobs
 */
class SendMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, JobTrait;
    
    protected $messages, $userId, $response;
    
    /**
     * SendMessage constructor.
     * @param array $messages
     * @param $userId
     */
    function __construct(array $messages, $userId) {
        
        $this->messages = $messages;
        $this->userId = $userId;
        $this->response = [
            'userId'     => $userId,
            'title'      => __('messages.message.title'),
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
                $results = Arr::collapse(
                    array_map(
                        function (Message $message) {
                            return $this->send($message, $this->response);
                        }, $this->messages
                    )
                );
                [$code, $msg] = $this->inform($results);
                $this->response['statusCode'] = $code;
                $this->response['message'] = $msg;
            });
            (new Broadcaster)->broadcast($this->response);
        } catch (Exception $e) {
            $this->eHandler($e, $this->response);
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
