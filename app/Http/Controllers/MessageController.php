<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\MessageRequest;
use App\Models\Department;
use App\Models\Media;
use App\Models\Message;
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
    
    protected $message, $department, $user, $media;
    
    public function __construct(
        Message $message, Department $departement,
        User $user, Media $media
    ) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->message = $message;
        $this->department = $departement;
        $this->user = $user;
        $this->media = $media;
        
    }
    
    /**
     * 消息列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->message->datatable()
            );
        }
        if (Request::method() == 'POST') {
            return $this->department->contacts();
        }
        
        return $this->output();
        
    }
    
    /**
     * 消息中心 (应用)
     *
     * @return void
     */
    public function message() {
        
        // return $this->output();
    }
    
    /**
     * 创建消息
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return response()->json(
                $this->department->tree()
            );
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
        
        return $this->message->sendMessage(
            $request->all()
        );
        
    }
    
    /**
     * 更新消息
     *
     * @param MessageRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(MessageRequest $request, $id) {
        
        return $this->result(
            $this->message->modify($request, $id)
        );
        
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
        abort_if(!$message, HttpStatusCode::NOT_FOUND);
        
        return $this->result($message->delete());
        
    }
    
    public function getDepartmentUsers() {
        
        return $this->department->showDepartments($this->checkRole());
        
    }
    
    private function checkRole($userId = 1) {
        
        $user = $this->user->find($userId);
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
        $firstIds = $this->department->whereParentId($id)->get(['id'])->toArray();
        if ($firstIds) {
            foreach ($firstIds as $firstId) {
                $childIds[] = $firstId['id'];
                $this->departmentChildIds($firstId['id']);
            }
        }
        
        return $childIds;
        
    }
    
    /**
     * 上传媒体文件
     *
     * @return JsonResponse
     */
    public function uploadFile() {
        
        return response()->json(
            $this->message->upload()
        );
        
    }
    
}
