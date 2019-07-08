<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Camera, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Filesystem\FileNotFoundException,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
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
    
    public $data, $userId, $response, $broadcaster;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param integer $userId
     * @throws PusherException
     */
    function __construct(array $data, $userId) {
        
        $this->data = $data;
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
        
        $camera = new Camera;
        try {
            array_map(
                function ($action, $userIds) use ($camera) {
                    foreach ($userIds as $userId) {
                        $user = User::find($userId);
                        switch ($action) {
                            case 'insert':
                                $params = [
                                    'uuid'         => $user->id,
                                    'name'         => $user->realname,
                                    'age'          => 0,
                                    'sex'          => $user->gender,
                                    'role'         => $user->face->state,
                                    'identity_num' => '',
                                    'csOther'      => '',
                                    'csICCard'     => $user->card ? $user->card->sn : '',
                                    'csTel'        => '',
                                    'csDep'        => '',
                                    'pStr'         => $this->image($user),
                                ];
                                $stored = false;
                                foreach ($this->cids($user) as $cid) {
                                    $face = $camera->invoke(join('/', [$action, $cid]), $params);
                                    if (!$stored) {
                                        Storage::disk('uploads')->put(
                                            $user->face->media->path, base64_decode($face['pStr'])
                                        );
                                    }
                                }
                                break;
                            case 'fmodify':
                                $detail = $camera->invoke('detail', $this->image($user));
                                Storage::disk('uploads')->put(
                                    $user->face->media->path, base64_decode($detail['csImage'])
                                );
                                $params = [
                                    'uuid'         => $user->id,
                                    'name'         => $user->realname,
                                    'age'          => 0,
                                    'sex'          => $user->gender,
                                    'role'         => $user->face->state,
                                    'identity_num' => '',
                                    'csOther'      => '',
                                    'csICCard'     => $user->card ? $user->card->sn : '',
                                    'csTel'        => '',
                                    'x'            => $detail['face_x'],
                                    'y'            => $detail['face_y'],
                                    'w'            => $detail['face_w'],
                                    'h'            => $detail['face_h'],
                                    'csImage'      => $this->image($user),
                                ];
                                foreach ($this->cids($user) as $cid) {
                                    $camera->invoke(join('/', [$action, $cid]), $params);
                                }
                                break;
                            case 'delete':
                                foreach ($this->cids(User::find($userId)) as $cid) {
                                    $camera->invoke(join('/', [$action, $cid, $userId]));
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }, ['insert', 'fmodify', 'delete'], $this->data
            );
        } catch (Exception $e) {
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
     * @param User $user
     * @return array
     */
    private function cids(User $user) {
        
        return Camera::whereIn(
            'id', $user->face->cameras->pluck('id')->toArray()
        )->pluck('cameraid')->toArray();
        
    }
    
    /**
     * @param User $user
     * @return string
     * @throws FileNotFoundException
     */
    private function image(User $user) {
        
        return base64_encode(
            Storage::disk('uploads')->get($user->face->media->path)
        );
        
    }
    
}