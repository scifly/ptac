<?php
namespace App\Jobs;

use App\Events\ContactImportTrigger;
use App\Models\App;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Educator;
use App\Models\EducatorClass;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Message;
use App\Models\MessageSendingLog;
use App\Models\Mobile;
use App\Models\Squad;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ManageSendMessage implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    
    /**
     * Create a new job instance.
     *
     * @param $data
     */
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function handle() {
//        $data = $this->data;
//        try {
//
//            DB::transaction(function () use ($data) {
//                $model = new Message();
//                $obj = explode(',', $data['departIds']);
//                $depts = [];
//                $users = [];
//                $us = [];
//                foreach ($obj as $o) {
//                    $item = explode('-', $o);
//                    if ($item[1]) {
//                        $users[] = User::find($item[1])->userid;
//                        $us[] = User::find($item[1])->id;
//                    } else {
//                        $depts[] = $o;
//                    }
//                }
//                $userItems = implode('|', $us);
//                $touser = implode('|', $users);
//                $toparty = implode('|', $depts);
//                # 推送的所有用户以及电话
//                $userDatas = $model->getMobiles($us, $depts);
//                $title = '';
//                $content = '';
//
//                $msl = [
//                    'read_count' => 0,
//                    'received_count' => 0,
//                    'recipient_count' => count($userDatas['users']),
//                ];
//                $id = MessageSendingLog::create($msl)->id;
//                foreach ($apps as $app) {
//                    $token = Wechat::getAccessToken($corp->corpid, $app['secret']);
//                    $message = [
//                        'touser' => $touser,
//                        'toparty' => $toparty,
//                        'agentid' => $app['agentid'],
//                    ];
//                    # 短信推送
//                    if ($data['type'] == 'sms') {
//                        $code = $this->sendSms($userItems, $toparty, $data['content']['sms']);
//                        $content = $data['content']['sms'] . '【成都外国语】';
//                        if ($code > 0) {
//                            $result = [
//                                'statusCode' => 200,
//                                'message' => '消息已发送！',
//                            ];
//                        } else {
//                            $result = [
//                                'statusCode' => 0,
//                                'message' => '短信推送失败！',
//                            ];
//                            return response()->json($result);
//
//                        }
//                    }else{
//                        switch ($data['type']) {
//                            case 'text' :
//                                $message['text'] = ['content' => $data['content']['text']];
//
//                                break;
//                            case 'image' :
//                            case 'voice' :
//                                $message['image'] = ['media_id' => $data['content']['media_id']];
//
//                                break;
//                            case 'mpnews' :
//                                $message['mpnews'] = ['articles' => $data['content']['articles']];
//                                $title = $data['content']['articles']['title'];
//                                break;
//                            case 'video' :
//                                $message['video'] = $data['content']['video'];
//                                $title = $data['content']['video']['title'];
//                                break;
//                        }
//                        $message['msgtype'] = $data['type'];
//                        $status = json_decode(Wechat::sendMessage($token, $message));
//                        $content = $message[$data['type']];
//
//                        if ($status->errcode == 0) {
//                            $result = [
//                                'statusCode' => 200,
//                                'message' => '消息已发送！',
//                            ];
//                        } else {
//                            $result = [
//                                'statusCode' => 0,
//                                'message' => '消息发送失败！',
//                            ];
//                            return response()->json($result);
//                        }
//
//                    }
//                    foreach ($userDatas['users'] as $i) {
//                        $comtype = $data['type'] == 'sms' ? '短信' : '应用';
//                        $read = $data['type'] == 'sms' ? 1 : 0;
//                        $sent = $result['statusCode'] == 200 ? 1 : 0;
//                        $mediaIds = $data['media_id'] == '' ? 0 : $data['media_id'];
//                        $m = [
//                            'comm_type_id' => CommType::whereName($comtype)->first()->id,
//                            'app_id' => $app['id'],
//                            'msl_id' => $id,
//                            'title' => $title,
//                            'content' => json_encode($content),
//                            'serviceid' => 0,
//                            'message_id' => 0,
//                            'url' => '',
//                            'media_ids' => $mediaIds,
//                            's_user_id' => $i->id,
//                            'r_user_id' => Auth::id(),
//                            'message_type_id' => MessageType::whereName('消息通知')->first()->id,
//                            'read' => $read,
//                            'sent' => $sent,
//                        ];
//                        $this->create($m);
//                    }
//
//                }
//                if ($result['statusCode'] == 200) {
//                    $readCount = $data['type'] == 'sms' ? count($userDatas['users']) : 0;
//                    $receivedCount = count($userDatas['users']);
//                    MessageSendingLog::find($id)->update(['read_count' => $readCount , 'received_count' => $receivedCount]);
//                }
//
//            });
//
//        }catch (Exception $e) {
//            throw $e;
//        }
//
//        return true;
        
    }
}
