<?php
namespace App\Jobs;

use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\Message;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SendMessage - 发送消息
 * @package App\Jobs
 */
class SendMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait, JobTrait;
    
    protected $message;
    
    /**
     * SendMessage constructor.
     *
     * @param Message $message
     */
    public function __construct(Message $message) {
        
        $this->message = $message;
        
    }
    
    /**
     * 消息发送任务
     *
     * @throws Exception
     * @throws Throwable
     */
    public function handle() {
        
        try {
            DB::transaction(function () {
                $this->sendMessage($this->message);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    
    
}
