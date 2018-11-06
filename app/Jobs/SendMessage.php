<?php
namespace App\Jobs;

use App\Helpers\HttpStatusCode;
use App\Helpers\JobTrait;
use App\Models\Message;
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
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
    
    protected $message, $response;
    
    /**
     * SendMessage constructor.
     * @param Message $message
     */
    function __construct(Message $message) {
        
        $this->message = $message;
        $this->response = [
            'userId' => $message->s_user_id,
            'title' => __('messages.message.title'),
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
                $this->send($this->message, $this->response);
            });
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
