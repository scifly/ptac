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
     * @return string
     */
    public function index() {
        // $userId = $this->getRole('http://weixin.028lk.com/message_center');
        // $user = $this->user->where('userid',$userId)->first();
        // if ($user->group->name == '教职员工'){
        //     $sendMessages = $this->message->where('s_user_id', $user->id)->get();
        //
        //     $receiveMessages = $this->message->where('r_user_id',$user->id)->get();
        
        return view('wechat.message_center.index');
        // }
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        
        return view('wechat.message_center.create');
    }
    
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        // $userId = $this->getRole('http://weixin.028lk.com/message_show');
        $userId = "yuanhongbin";
        $user = $this->user->where('userid',$userId)->first();
        $edit = $user->group->name == '教职员工' ? true : '';
        if (!$this->message->find($id)) {
            return $this->notFound();
        }
        
        return view('wechat.message_center.show', ['message' => $this->message->find($id),'edit' => $edit]);
    }
    
    /**
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse|null
     * @throws \Exception
     */
    public function destory($id){
        $message = $this->message->find($id);
        if (!$message) { return $this->notFound(); }

        //只能删除查看的记录 不能删除多媒体文件 多媒体文件路径被多个记录存入
        return $message->delete();
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
