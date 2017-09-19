<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Educator;
use App\Models\Media;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Array_;

/**
 * 消息
 *
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller {

    protected $message;
    protected $user;
    protected $media;

    public function __construct(Message $message, User $user, Media $media) {

        $this->message = $message;
        $this->user = $user;
        $this->media = $media;

    }

    /**
     * 消息列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->message->datatable());
        }
        return $this->output(__METHOD__);

    }

    /**
     * 创建消息
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {

        return $this->output(__METHOD__);

    }

    /**
     * 保存消息
     *
     * @param MessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MessageRequest $request) {

        return $this->message->store($request) ? $this->succeed() : $this->fail();

    }

    /**
     * 消息详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, [
            'message' => $message,
            'users' => $this->user->users($message->user_ids),
            'medias' => $this->media->educators($message->media_ids)
        ]);

    }

    /**
     * 编辑消息
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     *
     */
    public function edit($id) {

        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }

        return $this->output(__METHOD__, [
            'message' => $message,
            'selectedUsers' => $this->user->users($message->user_ids),
            'medias' => $this->media->medias($message->media_ids)
        ]);

    }

    /**
     * 更新消息
     *
     * @param MessageRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MessageRequest $request, $id) {

        return $this->message->modify($request, $id) ? $this->succeed() : $this->fail();

    }

    /**
     * 删除消息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        return $message->delete() ? $this->succeed() : $this->fail();

    }

    public function userMessages() {
        //判断用户是否为消息接收者
        $userId = 1;
        $messageTypes = ['成绩信息', '作业信息'];
        $userReceiveMessages = Array();
        foreach ($messageTypes as $messageType) {
            $userReceiveMessages[$messageType] = $this->userReceiveMessages($userId, $messageType);
        }
        dd($userReceiveMessages);
        return view('message.wechat_messgae',[$userReceiveMessages]);
    }

    private function userReceiveMessages($userId, $messageType) {
        //显示当前用户能接受到的消息
        $messages = $this->message->where('r_user_id', $userId)
            ->where('message_type_id', $messageType)->get();
    }

    private function userSendMessages() {
        //当前用户发送消息

    }
}
