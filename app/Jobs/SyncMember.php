<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, JobTrait, ModelTrait};
use App\Models\Corp;
use App\Models\User;
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
 * 企业号会员管理
 *
 * Class SyncMember
 * @package App\Jobs
 */
class SyncMember implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    protected $members, $userId, $response, $broadcaster;
    
    /**
     * Create a new job instance.
     *
     * @param array $members
     * @param integer $userId - 当前登录用户id
     * @throws PusherException
     */
    function __construct(array $members, $userId) {
        
        $this->members = $members;
        $this->userId = $userId;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $userId, '企业微信通讯录同步',
            Constant::OK, __('messages.synced'),
        ]);
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $results = [];
                foreach ($this->members as $member) {
                    [$params, $action] = $member;
                    $corps = Corp::whereIn('id', $params['corpIds'])->get();
                    foreach ($corps as $corp) {
                        !in_array($params['position'], ['运营', '企业'])
                            ?: $params['department'] = [$corp->departmentid];
                        [$errcode, $errmsg] = $this->sync($corp, $params, $action);
                        !$errcode ?: $results[] = array_values(
                            array_merge($params, ['result' => $errmsg])
                        );
                    }
                }
                if (sizeof($results) > 0) {
                    $this->response['statusCode'] = Constant::INTERNAL_SERVER_ERROR;
                    $this->response['message'] = __('messages.sync_failed');
                    $this->response['url'] = $this->filePath('failed_syncs') . '.xlsx';
                    $this->excel(
                        Arr::flatten($results), 'failed_syncs',
                        '同步失败记录', false
                    );
                }
            });
        } catch (Throwable $e) {
            $this->eHandler($this, $e);
        }
        !$this->userId ?: $this->broadcaster->broadcast($this->response);
        
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
    
    /**
     * 同步会员信息
     *
     * @param Corp $corp
     * @param mixed $params
     * @param string $action
     * @return bool|mixed
     * @throws Throwable
     */
    private function sync(Corp $corp, $params, $action) {
        
        # 获取access_token
        $token = Wechat::token('ent', $corp->corpid, Wechat::syncSecret($corp->id));
        if ($action != 'delete') unset($params['corpIds']);
        $data = $action == 'delete' ? $params['userid'] : $params;
        $result = $this->invoke($action, [$token], $data);
        # 企业微信通讯录不存在指定的会员，则创建该会员
        if ($result['errcode'] == 60111 && $action == 'update') {
            $result = $this->invoke('create', [$token], $params);
        }
        if (!$result['errcode'] && $action != 'delete') {
            User::whereUserid($params['userid'])->first()->update(['synced' => 1]);
            if ($action == 'update') {
                $member = $this->invoke('get', [$token, $params['userid']]);
                if (!$member['errcode'] && $member['status'] == 1) {
                    User::whereUserid($params['userid'])->first()->update([
                        'avatar_url' => $member['avatar'],
                        'subscribed' => 1,
                    ]);
                }
            }
        }
        $errmsg = !$result['errcode'] ? '' :
            join(':', [
                $corp->name,
                Constant::SYNC_ACTIONS[$action] . '会员',
                Constant::WXERR[$result['errcode']],
            ]);
        
        return [$result['errcode'], $errmsg];
        
    }
    
    /**
     * 调用微信接口
     *
     * @param string $method
     * @param array $values
     * @param null|string|array $data
     * @return mixed
     */
    private function invoke($method, array $values, $data = null) {
        
        return json_decode(
            Wechat::invoke(
                'ent', 'user', $method, $values, $data
            ), true
        );
        
    }
    
}