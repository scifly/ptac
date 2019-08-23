<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{App, DepartmentTag, Openid, School, Tag, TagUser, User};
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
    
    protected $data, $userId, $action;
    protected $response, $broadcaster, $app;
    
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
     * @throws Throwable
     */
    function handle() {
        
        try {
            $tag = Tag::find($this->data['tagid'][0]);
            $school = $tag->school;
            ($this->app = $school->app)
                ? $this->sync($this->app, $school)
                : $this->corpSync();
        } catch (Exception $e) {
            $this->eHandler($this, $e);
            throw $e;
        }
        $this->broadcaster->broadcast($this->response);
        
        return true;
    
    }
    
    /**
     * 公众号标签同步
     *
     * @param App $app
     * @param School $school
     * @throws Throwable
     */
    function sync(App $app, School $school) {
    
        try {
            DB::transaction(function () use ($app, $school) {
                $token = Wechat::token('pub', $app->appid, $app->appsecret);
                if ($this->action != 'delete') {
                    # 同步(创建/更新)标签
                    $tag = Tag::find($this->data['tagid'][0]);
                    $data['tag']['name'] = $tag->name;
                    $this->action != 'update' ?: $data['tag']['id'] = $tag->tagid;
                    $this->throw_if($result = $this->invoke($this->action, [$token], $data));
                    $this->action != 'create' ?: $tag->update(['tagid' => $result['tag']['id']]);
                    # 同步标签用户绑定关系
                    if ($this->action == 'update') {
                        # 获取标签用户绑定关系
                        $this->throw_if(
                            $result = $this->invoke('tag/get', [$token], [
                                'tagid' => $tag->tagid, 'next_openid' => ''
                            ])
                        );
                        # 删除标签用户绑定关系
                        if (!empty($openidList = $result['data']['openid'])) {
                            $this->throw_if(
                                $result = $this->invoke('members/batchuntagging', [$token], [
                                    'tagid' => $tag->tagid, 'openid_list' => $result['data']['openid']
                                ])
                            );
                        }
                    }
                    # 添加标签绑定的用户
                    $userIds = $tag->users->pluck('id')->toArray();
                    foreach ($tag->departments as $department) {
                        $userIds = array_merge(
                            $userIds, $department->users->pluck('id')->toArray()
                        );
                    }
                    $openids = Openid::whereIn('user_id', array_unique($userIds))
                        ->where('app_id', $app->id)
                        ->pluck('openid')->toArray();
    
                    empty($openids) ?: $this->throw_if(
                        $this->invoke(
                        'members/batchtagging', [$token], [
                            'openid_list' => $openids,
                            'tagid' => $tag->tagid
                        ])
                    );
                    # 设置同步标记
                    $tag->update(['synced' => 1]);
                } else {
                    array_map(
                        function ($tagId) use ($token) {
                            $this->throw_if(
                                $this->invoke($this->action, [$token, Tag::find($tagId)->tagid])
                            );
                        }, $this->data['tagid']
                    );
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
    
    }
    
    /**
     * 企业微信标签同步
     *
     * @throws Throwable
     */
    function corpSync() {
    
        try {
            DB::transaction(function () {
                $corp = School::find($this->data['school_id'])->corp;
                $token = Wechat::token('ent', $corp->corpid, Wechat::syncSecret($corp->id));
                $this->response['title'] .= '企业微信通讯录标签';
                $this->response['message'] .= '企业微信通讯录';
                $tag = Tag::find($this->data['tagid'][0]);
                if ($this->action != 'delete') {
                    $result = $this->invoke($this->action, [$token], $this->data);
                    if ($result['errcode'] == 40068 && $this->action == 'update') {
                        $result = $this->invoke('create', [$token], $this->data);
                    }
                    $this->throw_if($result);
                    $data = [
                        'tagid'     => $this->data[$tag->id],
                        'userlist'  => $tag->users->pluck('userid')->toArray(),
                        'partylist' => $tag->departments->pluck('id')->toArray(),
                    ];
                    if ($this->action == 'update') {
                        # 获取标签绑定的部门与用户
                        $this->throw_if($result = $this->invoke('get', [$token, $tag->id]));
                        $userlist = [];
                        foreach ($result['userlist'] as $user) {
                            $userlist[] = $user['userid'];
                        }
                        # 删除标签绑定的部门与用户
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
                    }
                    # 同步标签绑定的部门与用户
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
                    $tag->update(['synced' => 1]);
                } else {
                    array_map(
                        function ($tagId) use ($token) {
                            $this->throw_if($this->invoke($this->action, [$token, $tagId]));
                        }, $this->data['tagid']
                    );
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
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
        
        [$base, $category] = $this->app ? ['pub', 'tags'] : ['ent', 'tag'];
        return json_decode(
            Wechat::invoke(
                $base, $category, $method, $values, $data
            ), true
        );
        
    }
    
}