<?php
namespace App\Jobs;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\App;
use App\Models\Template;
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
 * Class GetTemplateList
 * @package App\Jobs
 */
class GetTemplateList implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    public $corpId, $userId, $response, $broadcaster;
    
    /**
     * Create a new job instance.
     *
     * @param $corpId
     * @param integer $userId
     * @throws PusherException
     */
    function __construct($corpId, $userId) {
        
        $this->corpId = $corpId;
        $this->userId = $userId;
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $this->userId, __('messages.template.title'),
            HttpStatusCode::OK, __('messages.template.completed'),
        ]);
        $this->broadcaster = new Broadcaster;
        
    }
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
        
        try {
            DB::transaction(function () {
                $apps = App::where(['corp_id' => $this->corpId, 'category' => 2])->get();
                /** @var App $app */
                foreach ($apps as $app) {
                    $templates = json_decode(
                        Wechat::invoke(
                            'pub', 'template', 'get_all_private_template',
                            [Wechat::token('pub', $app->appid, $app->appsecret)]
                        ), true
                    );
                    throw_if(
                        $errcode = $templates['errcode'],
                        new Exception(
                            join(':', [
                                __('messages.template.failed'),
                                Constant::WXERR[$errcode],
                            ])
                        )
                    );
                    foreach ($templates['template_list'] as $template) {
                        Template::updateOrCreate([
                            'app_id'     => $app->id,
                            'templateid' => $template['template_id'],
                            'enabled'    => Constant::ENABLED
                        ], [
                            'title'            => $template['title'],
                            'primary_industry' => $template['primary_industry'],
                            'deputy_industry'  => $template['deputy_industry'],
                            'content'          => $template['content'],
                            'example'          => $template['example'],
                        ]);
                    }
                }
            });
        } catch (Exception $e) {
            $this->eHandler($this, $e);
            throw $e;
        }
        $this->broadcaster->broadcast($this->response);
        
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
    
}