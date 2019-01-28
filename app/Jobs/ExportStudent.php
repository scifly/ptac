<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{CustodianStudent, Student};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Database\Eloquent\Collection,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\Log};
use Pusher\PusherException;
use Throwable;

/**
 * Class ExportStudent
 * @package App\Jobs
 */
class ExportStudent implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    public $data, $userId, $response, $broadcaster, $titles;
    
    /**
     * Create a new job instance.
     *
     * @param Collection|Student[] $data
     * @param array $titles
     * @param integer $userId
     * @throws PusherException
     */
    function __construct($data, $titles, $userId) {
        
        $this->data = $data;
        $this->titles = $titles;
        $this->userId = $userId;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $this->userId, __('messages.student.title'),
            HttpStatusCode::OK, __('messages.student.export_completed'),
        ]);
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
    
        $records = [];
        foreach ($this->data as $student) {
            if (!$student->user) continue;
            $cses = CustodianStudent::whereStudentId($student->id)->get();
            $relationships = [];
            foreach ($cses as $cs) {
                if (!$cs->custodian) continue;
                $cUser = $cs->custodian->user;
                $relationships[] = implode(':', [
                    $cs->relationship, $cUser->realname, $cUser->gender ? '男' : '女',
                    $cUser->mobiles->where('isdefault', 1)->first()->mobile,
                ]);
            }
            $sUser = $student->user;
            $records[] = [
                $sUser->realname,
                $sUser->gender ? '男' : '女',
                date('Y-m-d', strtotime($student->birthday)),
                $student->squad->grade->school->name,
                $student->squad->grade->name,
                $student->squad->name,
                $student->student_number,
                $student->card_number . "\t",
                $student->oncampus ? '住读' : '走读',
                $student->remark,
                !empty($relationships) ? implode(',', $relationships) : '',
            ];
        }
        # 按年级/班级/学号依次排序
        usort($records, function ($a, $b) {
            return strcmp($a[4], $b[4]) ?: strcmp($a[5], $b[5]) ?: strcmp($a[7], $b[7]);
        });
        $filename = 'student_exports';
        $this->excel(array_merge([$this->titles], $records), $filename, '学籍', false);
        $this->response['url'] = $this->filePath($filename) . '.xlsx';
        $this->broadcaster->broadcast($this->response);
        
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