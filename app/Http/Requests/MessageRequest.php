<?php
namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {
        
        return [
            'message_id' => 'required|integer',
            'url' => 'required|string|max:255',
            'message_type_id' => 'required|integer',
        ];
        
    }

    protected function prepareForValidation() {
        $input = $this->all();
        if (isset($input['media_ids'])) {
            $input['media_ids'] = implode(',', $input['media_ids']);
        }
        if (isset($input['selectedDepartments'])) {
            $input['r_user_id'] = $this->getInputUserIds($input['selectedDepartments']);
        }
        $input['url'] = "http://";
        $input['message_id'] = 0;
        $input['serviceid'] = 0;
        $input['read'] = 0;
        $input['sent'] = 0;
        $input['media_ids'] = 1;
        $input['s_user_id'] = 1;
        $input['message_type_id'] = 5;
        
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
