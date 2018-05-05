<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Facades\Wechat;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Grade;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\School;
use App\Models\Squad;
use App\Models\Student;
use App\Models\User;
use App\Rules\Mobiles;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class SendMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait;

    protected $data, $userIds, $deptIds, $userId, $corp, $apps, $message;
    
    /**
     * SendMessage constructor.
     *
     * @param array $data
     * @param array $userIds
     * @param array $deptIds
     * @param $userId
     * @param Corp|null $corp
     * @param array $apps
     * @param Message $message
     */
    public function __construct(
        array $data, array $userIds, array $deptIds, $userId,
        Corp $corp = null, array $apps = [], Message $message
    ) {
        
        $this->data = $data;
        $this->userIds = $userIds;
        $this->deptIds = $deptIds;
        $this->userId = $userId;
        $this->corp = $corp;
        $this->apps = $apps;
        $this->message = $message;
        
    }
    
    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function handle() {
    
        if ($this->data['type'] == 'sms') {
        
        } else {
            $toUserids = implode('|', User::whereIn('id', $this->userIds)->pluck('userid')->toArray());
            $toParties = implode('|', $this->deptIds);
        }
        
    }
    
}
