<?php
namespace App\Http\Requests;

use App\Models\CommType;
use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MessageRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {
    
        return [
            'comm_type_id'    => 'required|integer',
            'app_id'          => 'required|integer',
            'msl_id'          => 'required|integer',
            'title'           => 'required|string|max:64',
            'content'         => 'required|string|max:255',
            'serviceid'       => 'required|string|max:255',
            'message_id'      => 'required|integer',
            'url'             => 'required|string|max:255',
            'media_ids'       => 'required|string|max:255',
            's_user_id'       => 'required|integer',
            'r_user_id'       => 'required|integer',
            'message_type_id' => 'required|integer',
            'read'            => 'required|boolean',
            'sent'            => 'required|boolean',
            'user_ids'        => 'required|array',
            'dept_ids'        => 'required|array',
            'app_ids'         => 'required|array'
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['comm_type_id'] = $input['type'] == 'sms'
            ? CommType::whereName('短信')->first()->id
            : CommType::whereName('应用')->first()->id;
        $input['app_id'] = 0;
        $input['msl_id'] = 0;
        $input['title'] = $input['title'] ?? 'n/a';
        $input['serviceid'] = '0';
        $input['message_id'] = 0;
        $input['url'] = "http://";
        $input['media_ids'] = '0';
        $input['s_user_id'] = Auth::id();
        $input['r_user_id'] = '0';
        $input['message_type_id'] = 5;
        $input['read'] = 0;
        $input['sent'] = 0;
        $input['media_ids'] = $input['media_ids'] ? implode(',', $input['media_ids']) : [];
    
        if (isset($input['selectedDepartments'])) {
            $input['department_ids'] = $this->getInputUserIds($input['selectedDepartments']);
        }
        $this->replace($input);
        
    }
    
    /**
     *
     * 参数 传入选中的department和user的id
     * 返回 获得输入框中部门下的user和选择的user的id
     * @param $ids
     * @return array
     */
    private function getInputUserIds($ids) {
        
        $accordUserGetId = [];
        $accordDepartGetId = [];
        $str = 'UserId_';
        foreach ($ids as $userId) {
            #筛选出是user的id
            if (strpos($userId, $str) !== false) {
                $deleteHeaderNum = strstr($userId, $str);
                $accordUserGetId[] = substr($deleteHeaderNum, 7);
            } else {
                #这里是部门对应的userId
                $department = Department::find($userId);
                foreach ($department->users as $user) {
                    $accordDepartGetId[] = $user['id'];
                }
            }
        }
        
        return $receiveUserIds = array_unique(
            array_merge($accordUserGetId, $accordDepartGetId)
        );
        
    }
    
}
