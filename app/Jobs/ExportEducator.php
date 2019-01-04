<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Educator, EducatorClass};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Database\Eloquent\Collection,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels};
use Pusher\PusherException;
use ReflectionClass;
use Throwable;

/**
 * Class ExportEducator
 * @package App\Jobs
 */
class ExportEducator implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    public $data, $userId, $response, $broadcaster, $titles;
    
    /**
     * Create a new job instance.
     *
     * @param Collection|Educator[] $data
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
        foreach ($this->data as $educator) {
            if (!($user = $educator->user)) continue;
            list($grades, $squads) = array_map(
                function ($name) use ($educator) {
                    $className = 'App\\Models\\' . ucfirst($name);
                    $model = (new ReflectionClass($className))->newInstance();
                    /** @var Collection $collection */
                    $collection = $model->whereRaw($educator->id . ' IN (educator_ids)')->get();
                
                    return $collection->isEmpty() ? ''
                        : implode(',', $collection->pluck('name')->toArray());
                }, ['squad', 'grade']
            );
            $eces = EducatorClass::whereEducatorId($educator->id)->get();
            foreach ($eces as $ec) {
                $squad = $ec->squad;
                $subject = $ec->subject;
                if (isset($squad, $subject)) {
                    $cses[] = implode(':', [$squad->name, $subject->name]);
                }
            }
            $records[] = [
                $user->realname,
                $user->gender ? '男' : '女',
                strval($user->username),
                $user->position,
                $user->departments->first()->name,
                $educator->school->name,
                $user->mobiles->where('isdefault', 1)->first()->mobile,
                $grades,
                $squads,
                implode(',', $cses ?? [])
            ];
        }
        usort($records, function ($a, $b) {
            return strcmp($a[4], $b[4]);     # 按部门排序
        });
    
        $filename = 'educator_exports';
        $this->excel(array_merge($this->titles, $records), $filename, '教职员工', false);
        $this->response['url'] = 'uploads/' . date('Y/m/d/') . $filename . '.xlsx';
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