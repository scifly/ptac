<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Media;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller {
    protected $message;
    
    public function __construct(Message $message) { $this->message = $message; }
    
    /**
     * 显示消息列表
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
     * 显示创建新消息记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    public function store(MessageRequest $request) {
        
        $media_ids = $request->input('media_ids');
        $user_ids = $request->input('user_ids');
        $data = [
            'content' => $request->input('content'),
            'serviceid' => $request->input('serviceid'),
            'message_id' => $request->input('message_id'),
            'url' => $request->input('url'),
            'media_ids' => implode(',', $media_ids),
            'user_id' => $request->input('user_id'),
            'user_ids' => implode(',', $user_ids),
            'message_type_id' => $request->input('message_type_id'),
            'read_count' => 0,
            'received_count' => 0,
            'recipient_count' => 0,
        ];
        
        //删除原有的图片
        $del_ids = $request->input('del_ids');
        if ($del_ids) {
            $medias = Media::whereIn('id', $del_ids)->get(['id', 'path']);
            
            foreach ($medias as $v) {
                $path_arr = explode("/", $v->path);
                Storage::disk('uploads')->delete($path_arr[5]);
                
            }
            $delStatus = Media::whereIn('id', $del_ids)->delete();
        }
        if ($this->message->create($data)) {
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
    
    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $message = Message::whereId($id)->first();
        $f = explode(",", $message->user_ids);
        
        $users = User::whereIn('id', $f)->get(['id', 'realname']);
        
        $m = explode(",", $message->media_ids);
        
        $medias = Media::whereIn('id', $m)->get(['id', 'path']);
        return view('message.show', [
            'message' => $message,
            'users' => $users,
            'medias' => $medias,
        ]);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $message = $this->message->whereId($id)->first();
        $f = explode(",", $message->user_ids);
        
        $users = User::whereIn('id', $f)->get(['id', 'realname'])->toArray();
        
        $selectedUsers = [];
        foreach ($users as $value) {
            $selectedUsers[$value['id']] = $value['realname'];
        }
        $m = explode(",", $message->media_ids);
        
        $medias = Media::whereIn('id', $m)->get(['id', 'path']);
        
        return view('message.edit', [
            'js' => 'js/message/edit.js',
            'message' => $message,
            'selectedUsers' => $selectedUsers,
            'medias' => $medias,
            'form' => true
        
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param Message|\Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Message $message
     */
    public function update(MessageRequest $request, $id) {
        $data = Message::find($id);
        $media_ids = $request->input('media_ids');
        $user_ids = $request->input('user_ids');
        
        $data->content = $request->input('content');
        $data->serviceid = $request->input('serviceid');
        $data->message_id = $request->input('message_id');
        $data->url = $request->input('url');
        $data->media_ids = implode(',', $media_ids);
        $data->user_id = $request->input('user_id');
        $data->user_ids = implode(',', $user_ids);
        $data->message_type_id = $request->input('message_type_id');
        $data->read_count = 0;
        $data->received_count = 0;
        $data->recipient_count = 0;
        
        //删除原有的图片
        $del_ids = $request->input('del_ids');
        if ($del_ids) {
            $medias = Media::whereIn('id', $del_ids)->get(['id', 'path']);
            
            foreach ($medias as $v) {
                $path_arr = explode("/", $v->path);
                Storage::disk('uploads')->delete($path_arr[5]);
                
            }
            $delStatus = Media::whereIn('id', $del_ids)->delete();
        }
        if ($data->save()) {
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
            
        }
        
        return response()->json($this->result);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if ($this->message->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}
