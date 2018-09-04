<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\DepartmentTag;
use App\Models\Tag;
use App\Models\TagUser;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
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
    
    protected $data, $userId, $action, $response;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     * @param $action
     */
    public function __construct(array $data, $userId, $action) {
        
        $this->data = $data;
        $this->action = $action;
        $this->userId = $userId;
        $this->response = [
            'userId'     => $userId,
            'title'      => Constant::SYNC_ACTIONS[$action],
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.synced'),
        ];
    }
    
    /**
     * Execute the job
     *
     * @return bool
     * @throws Throwable
     */
    public function handle() {
    
        try {
            DB::transaction(function () {
                $corp = Corp::find($this->data['corp_id']);
                $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
                $this->throw_if($token);
                $this->response['title'] .= '企业微信通讯录标签';
                $this->response['message'] .= '企业微信通讯录';
                $accessToken = $token['access_token'];
                $action = $this->action . 'Tag';
                $result = json_decode(
                    Wechat::$action(
                        $token['access_token'],
                        $action != 'deleteTag' ? $this->data : $this->data['tagid']
                    )
                );
                # 企业微信通讯录不存在需要更新的标签，则创建该标签
                if ($result->{'errcode'} == 40068 && $action == 'updateTag') {
                    $result = json_decode(Wechat::createTag($accessToken, $this->data));
                }
                $this->throw_if($result);
                if ($action != 'deleteTag') {
                    # 如果成功创建/更新企业微信通讯录标签，则将本地通讯录相应标签的同步状态置为“已同步”
                    Tag::find($this->data['tagid'])->update(['synced' => 1]);
                    $tag = Tag::find($this->data['tagid']);
                    $data = [
                        'tagid' => $this->data['tagid'],
                        'userlist' => $tag->users->pluck('userid')->toArray(),
                        'partylist' => $tag->departments->pluck('id')->toArray()
                    ];
                    if ($tag->users->count() > 0 || $tag->departments->count() > 0) {
                        if ($action == 'createTag') {
                            $result = json_decode(Wechat::addTagMember($accessToken, $data));
                            $this->throw_if($result);
                            if (isset($result->{'invalidlist'})) {
                                foreach (explode('|', $result->{'invalidlist'}) as $userid) {
                                    TagUser::where([
                                        'user_id' => User::whereUserid($userid)->first()->id,
                                        'tag_id' => $tag->id
                                    ])->first()->update(['enabled' => 0]);
                                }
                            }
                            if (isset($result->{'invalidparty'})) {
                                foreach ($result->{'invalidparty'} as $departmentId) {
                                    DepartmentTag::where([
                                        'department_id' => $departmentId,
                                        'tag_id' => $tag->id
                                    ])->first()->update(['enabled' => 0]);
                                }
                            }
                        } else {
                            $result = json_decode(Wechat::getTagMember($accessToken, $tag->id));
                            $this->throw_if($result);
                            $userlist = [];
                            foreach ($result->{'userlist'} as $user) {
                                $userlist[] = $user->{'userid'};
                            }
                            $result = json_decode(
                                Wechat::delTagMember($accessToken, [
                                    'tagid' => $tag->id,
                                    'userlist' => $userlist,
                                    'partylist' => $result->{'partylist'}
                                ])
                            );
                            $this->throw_if($result);
                            $result = json_decode(
                                Wechat::addTagMember($accessToken, $data)
                            );
                            $this->throw_if($result);
                            if (isset($result->{'invalidlist'})) {
                                foreach (explode('|', $result->{'invalidlist'}) as $userid) {
                                    TagUser::where([
                                        'user_id' => User::whereUserid($userid)->first()->id,
                                        'tag_id' => $tag->id
                                    ])->first()->update(['enabled' => 0]);
                                }
                            }
                            if (isset($result->{'invalidparty'})) {
                                foreach ($result->{'invalidparty'} as $departmentId) {
                                    DepartmentTag::where([
                                        'department_id' => $departmentId,
                                        'tag_id' => $tag->id
                                    ])->first()->update(['enabled' => 0]);
                                }
                            }
                        }
                    }
                }
            });
        } catch (Exception $e) {
            $this->response['statusCode'] = $e->getCode();
            $this->response['message'] = $e->getMessage();
        }
        event(new JobResponse($this->response));
        
        return true;
        
    }
    
    /**
     * 抛出异常
     *
     * @param $result
     * @throws Throwable
     */
    private function throw_if($result) {
    
        throw_if(
            $result->{'errcode'},
            InternalErrorException::class,
            Constant::WXERR[$result->{'errcode'}]
        );
        
    }
    
}
