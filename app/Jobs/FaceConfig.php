<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Camera, CameraFace, Face, User};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Filesystem\FileNotFoundException,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB,
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
                foreach ($this->faces as $userId => $data) {
                    $user = User::find($userId);
                    if (isset($data['media_id'])) {
                        $data['user_id'] = $userId;
                        if (!$face = $user->face) {
                            $face = Face::create($data);
                            $user->update(['face_id' => $face->id]);
                            $action = 'insert';
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
                        } else {
                            $face->update($data);
                            $detail = $camera->invoke('detail', $this->image($user));
                            Storage::disk('uploads')->put(
                                $user->face->media->path, base64_decode($detail['csImage'])
                            );
                            $action = 'fmodify';
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
                        }
                        $cf->storeByFaceId($face->id, $data['cameraids']);
                        foreach ($this->cids($user) as $cid) {
                            $camera->invoke(join('/', [$action, $cid]), $params);
                        }
                    } elseif ($user->face) {
                        foreach ($this->cids($user) as $cid) {
                            $camera->invoke(join('/', ['delete', $cid, $userId]));
                        }
                        CameraFace::whereFaceId($user->face_id)->delete();
                        $user->update(['face_id' => 0]);
                        $user->face->delete();
                    }
                }
            });
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
        
        $paths = explode('/', $user->face->media->path);
        unset($paths[0]);
        
        return base64_encode(
            Storage::disk('uploads')->get(join('/', $paths))
        );
        
    }
    
}