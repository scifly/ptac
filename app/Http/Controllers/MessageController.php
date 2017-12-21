<?php
namespace App\Http\Controllers;

use App\Helpers\ControllerTrait;
use App\Http\Requests\MessageRequest;
use App\Models\CommType;
use App\Models\Department;
use App\Models\Media;
use App\Models\Message;
use App\Models\School;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 消息
 *
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller {
    use ControllerTrait;
    protected $message;
    protected $user;
    protected $media;
    protected $department;
    
    public function __construct(Message $message, User $user, Media $media, Department $department) {
    
        $this->middleware(['auth']);
        $this->message = $message;
        $this->user = $user;
        $this->media = $media;
        $this->department = $department;
        
    }
    
    /**
     * 消息列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->message->datatable());
        }
        if (Request::method() == 'POST') {
            return $this->department->contacts();
//            $school = School::find(1);
//            return $this->department->tree($school->department_id);
        }


        
        return $this->output();
        
    }
    
    /**
     * 发送消息
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function send() {
    
        return $this->output();
    
    }
    
    /**
     * 创建消息
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return $this->department->tree();
        }
        
        return $this->output();
        
    }
    
    /**
     * 保存消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(MessageRequest $request) {
        
//        $commTypeName = ['微信', '短信', '应用'];
        $input = $request->all();
//        $commType = CommType::whereId($input['comm_type_id'])->first();
//        if ($commType->name == $commTypeName[0]) {
//            return $this->message->store($request) ? $this->succeed() : $this->fail();
//        }
        return $this->message->sendText($input);
        
    }
    
    /**
     * 消息详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $message = $this->message->find($id);
        if (!$message) { return $this->notFound(); }
        
        return $this->output([
            'message' => $message,
            'users'   => $this->user->users($message->user_ids),
            'medias'  => $this->media->educators($message->media_ids),
        ]);
        
    }
    
    /**
     * 编辑消息
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $message = $this->message->find($id);
        if (!$message) { return $this->notFound(); }
        
        return $this->output([
            'message'       => $message,
            'selectedUsers' => $this->user->users($message->user_ids),
            'medias'        => $this->media->medias($message->media_ids),
        ]);
        
    }
    
    /**
     * 更新消息
     *
     * @param MessageRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(MessageRequest $request, $id) {
        
        return $this->message->modify($request, $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除消息
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        
        return $message->delete() ? $this->succeed() : $this->fail();
        
    }
    
    public function getDepartmentUsers() {
        
        return $this->department->showDepartments($this->checkRole());
    }
    
    private function checkRole($userId = 1) {
        
        $user = User::find($userId);
        $departments = [];
        $childDepartmentId = [];
        foreach ($user->departments as $department) {
            $departments[] = $department['id'];
        }
        foreach ($departments as $departmentId) {
            $childDepartmentId = $this->departmentChildIds($departmentId);
        }
        $departmentIds = array_merge($departments, $childDepartmentId);
        
        return array_unique($departmentIds);
    }
    
    /**
     * 获取该部门下所有部门id
     * @param $id
     * @return array
     */
    private function departmentChildIds($id) {
        static $childIds = [];
        $firstIds = Department::where('parent_id', $id)->get(['id'])->toArray();
        if ($firstIds) {
            foreach ($firstIds as $firstId) {
                $childIds[] = $firstId['id'];
                $this->departmentChildIds($firstId['id']);
            }
        }
        
        return $childIds;
    }

//    public function getDepartmentUsers() {
//        $input = Request::all();
//        $departmentUsers = Array();
//        //找出此部门节点下的所有子节点的id
//        $departmentIds = $this->departmentChildIds($input['id']);
//        $departmentIds[] = $input['id'];
//        foreach ($departmentIds as $departmentId) {
//            $department = Department::find($departmentId);
//            foreach ($department->users as $user) {
//                $departmentUsers[$user['id']] = $user['username'];
//            }
//        }
//        //dd($departmentUsers);
//        $dataView = view('message.wechat_message', ['departmentUsers' => $departmentUsers])->render();
//        return is_null($departmentUsers) ? $this->fail('该部门下暂时还没有人员') : $this->succeed($dataView);
//    }
//    public function userMessages() {
//        //判断用户是否为消息接收者
//        $userId = 1;
//        $messageTypes = ['成绩信息', '作业信息'];
//        $userReceiveMessages = Array();
//        foreach ($messageTypes as $messageType) {
//            $userReceiveMessages[$messageType] = $this->userReceiveMessages($userId, $messageType);
//        }
//        dd($userReceiveMessages);
//        return view('message.wechat_messgae', [$userReceiveMessages]);
//    }
    private function userReceiveMessages($userId, $messageType) {
        //显示当前用户能接受到的消息
        $messages = $this->message->where('r_user_id', $userId)
            ->where('message_type_id', $messageType)->get();
    }

    public function uploadFile() {
        $file = Request::file('uploadFile');
        if (empty($file)) {
            $result['statusCode'] = 0;
            $result['message'] = '您还未选择文件！';

            return $result;
        } else {
            $result['data'] = [];
            $mes = $this->uploadedMedias($file, '消息中心');
            $result['statusCode'] = 1;
            $result['message'] = '上传成功！';
            $result['data'] = $mes;
        }

        return response()->json($result);
    }
}
