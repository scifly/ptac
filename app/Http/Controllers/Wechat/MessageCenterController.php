<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class MessageCenterController extends Controller {
    
    protected $message, $user;
    
    /**
     * MessageCenterController constructor.
     * @param Message $message
     * @param User $user
     */
    public function __construct(Message $message, User $user) {
        // $this->middleware();
        $this->message = $message;
        $this->user = $user;
        
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index() {

        $userId = 'kobe';
        $user = User::whereUserid($userId)->first();
        if(Request::isMethod('post')){
            $keywords = Request::get('keywords');
            $type = Request::get('type');
            if(!empty($keywords)){
                switch ($type){
                    case 'send':
                        $sendMessages = [];
                        $sendMessages = Message::whereSUserId($user->id)
                            ->where('content', 'like', '%'.$keywords.'%')
                            ->orWhere('title', 'like', '%' . $keywords . '%')
                            ->get();

                        if(sizeof($sendMessages) != 0){
                            foreach ($sendMessages as $s){
                                  $s['r_user_id'] = User::whereId($s['r_user_id'])->first()->realname;
                            }
                        }

                        return response(['sendMessages' => $sendMessages,'type'=> $type]);
                        break;
                    case 'receive':
                        $receiveMessages = [];
                        $receiveMessages = Message::whereRUserId($user->id)
                            ->where('content', 'like', '%' . $keywords . '%')
                            ->orWhere('title', 'like', '%' . $keywords . '%')
                            ->get();
                        if(sizeof($receiveMessages) != 0){
                            foreach ($receiveMessages as $r){
                                $r['s_user_id'] = User::whereId($r['s_user_id'])->first()->realname;
                            }
                        }
                        return response(['type' => $type, 'receiveMessages' => $receiveMessages]);
                        break;
                    default:
                        break;
                }

            }


        }
        //判断是否为教职工
        $educator = true;
        // if($user->group->name == '教职工'){
        //     $educator = true;
        // }
        $sendMessages = $this->message->where('s_user_id', $user->id)->get()->groupBy('message_type_id');
        $receiveMessages = $this->message->where('r_user_id', $user->id)->get()->groupBy('message_type_id');
        $count = $this->message->where('r_user_id', $user->id)->where('readed', '0')->count();
        
        return view('wechat.message_center.index', [
            'receiveMessages' => $receiveMessages,
            'sendMessages'    => $sendMessages,
            'count'           => $count,
            'educator'        => $educator,
        ]);
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        
        return view('wechat.message_center.create');
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store() {
        dd(Request::all());
        return view('wechat.message_center.create');
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        
        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        
        return view('wechat.message_center.create', ['message' => $message]);
    }
    
    /**
     * @param $id
     */
    public function update($id) {
    
    }
    
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus($id) {
        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        $message->readed = 1;
        
        return $message->save() ? self::succeed() : self::fail();
    }
    
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        // $userId = $this->getRole('http://weixin.028lk.com/message_show');
        $userId = "kobe";
        $user = $this->user->where('userid', $userId)->first();
        $message = $this->message->find($id);
        $edit = $user->id == $message->s_user_id ? true : false;
        
        return view('wechat.message_center.show', ['message' => $this->message->find($id), 'edit' => $edit]);
    }
    
    /**
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse|null
     * @throws \Exception
     */
    public function destory($id) {
        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        
        //只能删除查看的记录 不能删除多媒体文件 多媒体文件路径被多个记录存入
        return $message->delete() ? self::succeed() : self::fail();
    }
    
    /**
     * @param $calbackUrl
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     */
    private function getRole($calbackUrl) {
        //获取微信用户信息
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        $agentId = 3;
        $code = Request::input('code');
        if (empty($code)) {
            $codeUrl = Wechat::getCodeUrl($corpId, $agentId, $calbackUrl);
            
            return redirect($codeUrl);
        } else {
            $code = Request::get('code');
            $accessToken = Wechat::getAccessToken($corpId, $secret);
            $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
            
            return $userInfo['userId'];
            //{
            //"UserId":"yuanhongbin",
            //"DeviceId":"873fbe89aec047758ebbc6b41e4b45f9",
            //"errcode":0,
            //"errmsg":"ok",
            //"user_ticket":"xVAunChCFmwvi1yJokTRDtzOuIyyvJuYQ2Q59LjiUB-b1O9rJZlx0pGXrNPGjGv7h13abTLvn30oxT-BqvY7YQ",
            //"expires_in":1800
            //}
            // }
        }
        
    }
    
}
