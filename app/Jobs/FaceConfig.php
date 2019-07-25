<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Camera, CameraFace, Face, Media, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Filesystem\FileNotFoundException,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB,
    Support\Facades\Log,
    Support\Facades\Storage};
use Pusher\PusherException;
use Throwable;

/**
 * Class ExportEducator
 * @package App\Jobs
 */
class FaceConfig implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    public $faces, $userId, $response, $broadcaster;
    
    /**
     * Create a new job instance.
     *
     * @param array $faces
     * @param integer $userId
     * @throws PusherException
     */
    function __construct(array $faces, $userId) {
        
        $this->faces = $faces;
        $this->userId = $userId;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $this->userId, __('messages.face.title'),
            HttpStatusCode::OK, __('messages.face.config_completed'),
        ]);
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
    
        try {
            DB::transaction(function () {
                $camera = new Camera;
                $cf = new CameraFace;
                $failed = [];
                foreach ($this->faces as $userId => $data) {
                    $user = User::find($userId);
                    $cids = $data['cameraids'];
                    !in_array(0, $cids)
                        ?: $cids = $camera->all()->pluck('id')->toArray();
                    if (isset($data['media_id'])) {
                        $data['user_id'] = $userId;
                        if (!$face = $user->face) {
                            # 新增
                            $face = Face::create($data);
                            Log::debug($face->id);
                            $user->update(['face_id' => $face->id]);
                            $action = 'insert';
                            $params = [
                                'uuid'         => strval($userId),
                                'name'         => $user->realname,
                                'age'          => 0,
                                'sex'          => $user->gender,
                                'role'         => $data['state'],
                                'identity_num' => '',
                                'csOther'      => '',
                                'csICCard'     => $user->card ? $user->card->sn : '',
                                'csTel'        => '',
                                'csDep'        => '',
                                'pStr'         => $this->image($data['media_id']),
                            ];
                        } else {
                            # 更新
                            $face->update($data);
                            $detail = $camera->invoke(
                                'detail', $this->image($data['media_id'])
                            );
                            throw_if(
                                isset($detail['success']),
                                new Exception(__('messages.face.detail_not_found'))
                            );
                            Storage::disk('uploads')->put(
                                $this->path($user), base64_decode($detail['csImage'])
                            );
                            $action = 'fmodify';
                            $params = [
                                'uuid'         => strval($userId),
                                'name'         => $user->realname,
                                'age'          => 0,
                                'sex'          => $user->gender,
                                'role'         => $data['state'],
                                'identity_num' => '',
                                'csOther'      => '',
                                'csICCard'     => $user->card ? $user->card->sn : '',
                                'csTel'        => '',
                                'x'            => $detail['face_x'],
                                'y'            => $detail['face_y'],
                                'w'            => $detail['face_w'],
                                'h'            => $detail['face_h'],
                                'csImage'      => $this->image($data['media_id']),
                            ];
                        }
                        $cf->storeByFaceId($face->id, $data['cameraids']);
                        foreach ($cids as $cid) {
                            $result = $camera->invoke(join('/', [$action, $cid]), $params);
                            $result['success'] ?: $failed[] = $this->err($user, $cid, $result);
                        }
                    } elseif ($user->face) {
                        # 删除
                        foreach ($cids($user) as $cid) {
                            $result = $camera->invoke(join('/', ['delete', $cid, $userId]));
                            $result['success'] ?: $failed[] = $this->err($user, $cid, $result);
                        }
                        $cf->whereFaceId($user->face_id)->delete();
                        $user->update(['face_id' => 0]);
                        $user->face->delete();
                    }
                }
                throw_if(
                    !empty($failed),
                    new Exception(sprintf(
                        __('messages.face.config_failed'),
                        json_encode($failed, JSON_UNESCAPED_UNICODE)
                    ))
                );
            });
        } catch (Exception $e) {
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->response['message'] = $e->getMessage();
            $this->broadcaster->broadcast($this->response);
            throw $e;
        }
        
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
    
    /**
     * @param $mediaId
     * @return string
     * @throws FileNotFoundException
     */
    private function image($mediaId) {
        
        return base64_encode(
            Storage::disk('uploads')->get($this->path($mediaId))
        );
        
    }
    
    /**
     * 返回人脸图片相对路径
     *
     * @param $mediaId
     * @return string
     */
    private function path($mediaId) {
    
        $paths = explode('/', Media::find($mediaId)->path);
        unset($paths[0]);
        
        return join('/', $paths);
        
    }
    
    /**
     * 返回错误消息
     *
     * @param User $user
     * @param $cid
     * @param array $result
     * @return array
     */
    private function err(User $user, $cid, array $result) {
        
        return  [
            $user->realname,
            Camera::find($cid)->name,
            $result['result'] . ':' . $result['msg']
        ];
        
    }
    
}