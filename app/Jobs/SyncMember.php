<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\Corp;
use App\Models\User;
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
            HttpStatusCode::OK, __('messages.synced'),
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
                    list($params, $action) = $member;
                    $corps = Corp::whereIn('id', $params['corpIds'])->get();
                    foreach ($corps as $corp) {
                        !in_array($params['position'], ['运营', '企业'])
                            ?: $params['department'] = [$corp->departmentid];
                        list($errcode, $errmsg) = $this->sync($corp, $params, $action);
                        !$errcode ?: $results[] = array_values(
                            array_merge($params, ['result' => $errmsg])
                        );
                    }
                }
                if (sizeof($results) > 0) {
                    $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
                    $this->response['message'] = __('messages.sync_failed');
                    $this->response['url'] = $this->filePath('failed_syncs') . '.xlsx';
                    $this->excel(array_flatten($results), 'failed_syncs', '同步失败记录', false);
                }
            });
        } catch (Throwable $e) {
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->response['message'] = $e->getMessage();
            !$this->userId ?: $this->broadcaster->broadcast($this->response);
            throw $e;
        }
        !$this->userId ?: $this->broadcaster->broadcast($this->response);
        
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
    
    /**
     * 同步会员信息
     *
     * @param Corp $corp
     * @param mixed $params
     * @param string $action
     * @return bool|mixed
     */
    private function sync(Corp $corp, $params, $action) {
        
        # 获取access_token
        $token = Wechat::getAccessToken(
            $corp->corpid,
            $corp->contact_sync_secret,
            true
        );
        if ($token['errcode']) return array_values($token);
        $accessToken = $token['access_token'];
        if ($action != 'delete') unset($params['corpIds']);
        $api = $action . 'User';
        $data = $api == 'deleteUser' ? $params['userid'] : $params;
        $result = json_decode(
            Wechat::$api($accessToken, $data), true
        );
        # 企业微信通讯录不存在指定的会员，则创建该会员
        if ($result['errcode'] == 60111 && $api == 'updateUser') {
            $result = json_decode(
                Wechat::createUser($accessToken, $params), true
            );
        }
        if (!$result['errcode'] && $api != 'deleteUser') {
            User::whereUserid($params['userid'])->first()->update(['synced' => 1]);
            if ($api == 'updateUser') {
                $member = json_decode(
                    Wechat::getUser($accessToken, $params['userid']), true
                );
                if (!$member['errcode'] && $member['status'] == 1) {
                    User::whereUserid($params['userid'])->first()->update([
                        'avatar_url' => $member['avatar'],
                        'subscribed' => 1,
                    ]);
                }
            }
        }
        $errmsg = !$result['errcode'] ? '' :
            implode(':', [
                $corp->name,
                Constant::SYNC_ACTIONS[$action] . '会员',
                Constant::WXERR[$result['errcode']],
            ]);
        
        return [$result['errcode'], $errmsg];
        
    }
    
}