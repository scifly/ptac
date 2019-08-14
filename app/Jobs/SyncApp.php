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
            'userId' => $this->userId,
            'title' => __('messages.app.title'),
            'message' => __('messages.app.app_configured'),
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
                if ($this->data['token']) {
                    # 创建 / 更新公众号记录
                    !$this->id
                        ? App::create($this->data)
                        : App::find($this->id)->update($this->data);
                } else {
                    # 同步并创建 / 更新企业应用记录
                    $token = Wechat::token(
                        'ent',
                        Corp::find($this->data['corp_id'])->corpid,
                        $this->data['secret']
                    );
                    throw_if(
                        $token['errcode'],
                        new Exception(Constant::WXERR[$token['errcode']])
                    );
                    if (!$this->id) {
                        $method = 'get';
                        $values = [$token, $this->data['agentid']];
                    } else {
                        $method = 'set';
                        $values = [$token];
                        $data = [
                            'agentid' => $this->data['agentid'],
                            'report_location_flag' => $this->data['report_location_flag'],
                            'name' => $this->data['name'],
                            'description' => $this->data['description'],
                            'redirect_domain' => $this->data['redirect_domain'],
                            'isreportenter' => $this->data['isreportenter'],
                            'home_url' => $this->data['home_url'],
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
                    if (!$this->id) {
                        $this->data['name'] = $result['name'];
                        $this->data['square_logo_url'] = $result['square_logo_url'];
                        $this->data['allow_userinfos'] = json_encode(
                            $result['allow_userinofs'], JSON_UNESCAPED_UNICODE
                        );
                        $this->data['allow_partys'] = json_encode($result['allow_partys']);
                        $this->data['allow_tags'] = json_encode($result['allow_tags']);
                        $this->data['close'] = $result['close'];
                        $this->data['redirect_domain'] = $result['redirect_domain'];
                        $this->data['report_location_flag'] = $result['report_location_flag'];
                        $this->data['home_url'] = $result['home_url'];
                        App::create($this->data);
                    } else {
                        App::find($this->id)->update($this->data);
                    }
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
