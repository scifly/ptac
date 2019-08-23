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
    Queue\SerializesModels};
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
     * Execute the job
     *
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
    
        try {
            $records = [];
            foreach ($this->data as $student) {
                if (!$student->user) continue;
                $cses = CustodianStudent::whereStudentId($student->id)->get();
                $relationships = [];
                foreach ($cses as $cs) {
                    if (!$cs->custodian) continue;
                    $cUser = $cs->custodian->user;
                    $relationships[] = implode(':', [
                        $cs->relationship, $cUser->realname,
                        $cUser->gender ? '男' : '女', $cUser->mobile,
                    ]);
                }
                $sUser = $student->user;
                $records[] = [
                    $sUser->realname,
                    $sUser->gender ? '男' : '女',
                    $student->squad->grade->school->name,
                    date('Y-m-d', strtotime($student->birthday)),
                    $student->squad->grade->name,
                    $student->squad->name,
                    $student->sn,
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
        } catch (Exception $e) {
            $this->eHandler($this, $e);
        }
        $this->broadcaster->broadcast($this->response);
        
        return true;
        
    }
    
    /**
     * 任务异常处理
     *
     * @param Exception $e
     * @throws Exception
     */
    function failed(Exception $e) {
        
        $this->eHandler($this, $e);
        
    }
    
}