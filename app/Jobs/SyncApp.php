<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait};
use App\Models\{App, Corp};
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
 * Class SyncApp
 * @package App\Jobs
 */
class SyncApp implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobTrait;
    
    protected $data, $userId, $id, $response, $broadcaster;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId - 接收广播消息的用户id
     * @param null|integer $id - 应用id
     * @throws PusherException
     */
    function __construct(array $data, $userId, $id = null) {
        
        $this->data = $data;
        $this->userId = $userId;
        $this->id = $id;
        $this->response = [
            'userId'     => $this->userId,
            'title'      => __('messages.app.title'),
            'message'    => __('messages.app.configured'),
            'statusCode' => HttpStatusCode::OK,
        ];
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * Execute the job
     *
     * @return mixed
     * @throws PusherException
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $category = $this->data['category'];
                if ($category == 1) {
                    # 企业应用
                    $token = Wechat::token(
                        'ent',
                        Corp::find($this->data['corp_id'])->corpid,
                        $this->data['appsecret']
                    );
                    if (!$this->id || !(App::find($this->id)->properties)) {
                        $method = 'get';
                        $values = [$token, $this->data['appid']];
                    } else {
                        $method = 'set';
                        $values = [$token];
                        $data = [
                            'agentid'              => $this->data['appid'],
                            'name'                 => $this->data['name'],
                            'report_location_flag' => $this->data['report_location_flag'],
                            'description'          => $this->data['description'],
                            'redirect_domain'      => $this->data['redirect_domain'],
                            'isreportenter'        => $this->data['isreportenter'],
                            'home_url'             => $this->data['home_url'],
                        ];
                    }
                    $result = json_decode(
                        Wechat::invoke(
                            'ent', 'agent', $method,
                            $values, $data ?? null
                        ), true
                    );
                    throw_if(
                        $result['errcode'],
                        new Exception(Constant::WXERR[$result['errcode']])
                    );
                    if ($method == 'get') {
                        unset($result['errcode']);
                        unset($result['errmsg']);
                        unset($result['name']);
                        $this->data['properties'] = json_encode(
                            $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        );
                    }
                    if (!$this->id) {
                        App::create($this->data);
                    } else {
                        $app = App::find($this->id);
                        !$app->properties
                            ?: $this->data = array_merge(
                            $this->data, [
                                'properties->report_location_flag' => $this->data['report_location_flag'],
                                'properties->redirect_domain'      => $this->data['redirect_domain'],
                                'properties->isreportenter'        => $this->data['isreportenter'],
                                'properties->home_url'             => $this->data['home_url'],
                            ]
                        );
                        $app->update($this->data);
                    }
                } else {
                    # "公众号"或"通讯录同步"
                    $this->data['properties'] = [
                        'token'            => $this->data['token'],
                        'encoding_aes_key' => $this->data['encoding_aes_key'],
                    ];
                    $category != 3 ?: $this->data['properties'] = array_merge(
                        ['url' => $this->data['url']],
                        $this->data['properties']
                    );
                    $this->data['properties'] = json_encode(
                        $this->data['properties'],
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    );
                    !$this->id
                        ? App::create($this->data)
                        : App::find($this->id)->update($this->data);
                }
            });
        } catch (Exception $e) {
            $this->response['mesage'] = $e->getMessage();
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->broadcaster->broadcast($this->response);
            throw $e;
        }
        $this->broadcaster->broadcast($this->response);
        
        return true;
        
    }
    
    /**
     * @param Exception $exception
     * @throws PusherException
     */
    function failed(Exception $exception) {
        
        $this->eHandler($exception, $this->response);
        
    }
    
}
