<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Corp, DepartmentTag, Tag, TagUser, User};
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
 * 企业微信通讯录标签同步
 *
 * Class SyncDepartment
 * @package App\Jobs
 */
class SyncTag implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    protected $data, $userId, $action, $response, $broadcaster;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     * @param $action
     * @throws PusherException
     */
    function __construct(array $data, $userId, $action) {
        
        $this->data = $data;
        $this->action = $action;
        $this->userId = $userId;
        $this->response = [
            'userId'     => $userId,
            'title'      => Constant::SYNC_ACTIONS[$action],
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.synced'),
        ];
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * Execute the job
     *
     * @return bool
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $corp = Corp::find($this->data['corp_id']);
                $token = Wechat::token('ent', $corp->corpid, Wechat::syncSecret($corp->id));
                $this->response['title'] .= '企业微信通讯录标签';
                $this->response['message'] .= '企业微信通讯录';
                if ($this->action != 'delete') {
                    $this->data['tagid'] = $this->data['tagid'][0];
                    $result = $this->invoke($this->action, [$token], $this->data);
                    # 企业微信通讯录不存在需要更新的标签，则创建该标签
                    if ($result['errcode'] == 40068 && $this->action == 'update') {
                        $result = $this->invoke('create', [$token], $this->data);
                    }
                    $this->throw_if($result);
                    # 如果成功创建/更新企业微信通讯录标签，则将本地通讯录相应标签的同步状态置为“已同步”
                    Tag::find($this->data['tagid'])->update(['synced' => 1]);
                    $tag = Tag::find($this->data['tagid']);
                    $data = [
                        'tagid'     => $this->data['tagid'],
                        'userlist'  => $tag->users->pluck('userid')->toArray(),
                        'partylist' => $tag->departments->pluck('id')->toArray(),
                    ];
                    if ($tag->users->count() > 0 || $tag->departments->count() > 0) {
                        if ($this->action == 'create') {
                            $this->throw_if(
                                $result = $this->invoke('addtagusers', [$token], $data)
                            );
                            if (isset($result['invalidlist'])) {
                                foreach (explode('|', $result['invalidlist']) as $userid) {
                                    TagUser::where([
                                        'user_id' => User::whereUserid($userid)->first()->id,
                                        'tag_id'  => $tag->id,
                                    ])->first()->update(['enabled' => 0]);
                                }
                            }
                            if (isset($result['invalidparty'])) {
                                foreach ($result['invalidparty'] as $departmentId) {
                                    DepartmentTag::where([
                                        'department_id' => $departmentId,
                                        'tag_id'        => $tag->id,
                                    ])->first()->update(['enabled' => 0]);
                                }
                            }
                        } else {
                            $this->throw_if(
                                $result = $this->invoke('get', [$token, $tag->id])
                            );
                            $userlist = [];
                            foreach ($result['userlist'] as $user) {
                                $userlist[] = $user['userid'];
                            }
                            if (!empty($userlist) || !empty($result['partylist'])) {
                                $this->throw_if(
                                    $result = $this->invoke(
                                        'deltagusers', [$token], [
                                        'tagid'     => $tag->id,
                                        'userlist'  => $userlist,
                                        'partylist' => $result['partylist'],
                                    ])
                                );
                            }
                            $this->throw_if($result = $this->invoke('addtagusers', [$token], $data));
                            if (isset($result['invalidlist'])) {
                                foreach (explode('|', $result['invalidlist']) as $userid) {
                                    TagUser::where([
                                        'user_id' => User::whereUserid($userid)->first()->id,
                                        'tag_id'  => $tag->id,
                                    ])->first()->update(['enabled' => 0]);
                                }
                            }
                            if (isset($result['invalidparty'])) {
                                foreach ($result['invalidparty'] as $departmentId) {
                                    DepartmentTag::where([
                                        'department_id' => $departmentId,
                                        'tag_id'        => $tag->id,
                                    ])->first()->update(['enabled' => 0]);
                                }
                            }
                        }
                    }
                } else {
                    array_map(
                        function ($tagId) use ($token) {
                            $this->invoke('delete', [$token, $tagId]);
                        }, $this->data['tagid']
                    );
                }
                $this->broadcaster->broadcast($this->response);
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
    
    /**
     * 抛出异常
     *
     * @param $result
     * @throws Throwable
     */
    private function throw_if($result) {
        
        throw_if(
            $errcode = $result['errcode'],
            Exception::class,
            Constant::WXERR[$errcode]
        );
        
    }
    
    /**
     * 调用微信接口
     *
     * @param string $method
     * @param array $values
     * @param null|string|array $data
     * @return mixed
     */
    private function invoke($method, $values, $data = null) {
        
        return json_decode(
            Wechat::invoke(
                'ent', 'tag', $method, $values, $data
            ), true
        );
        
    }
    
}