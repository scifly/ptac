<?php
namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest {
    
    protected $rules = [
//        'content'         => 'required|string|max:255',
        //'serviceid' => 'required|string|max:255',
        //'message_id' => 'required|integer',
        //'url' => 'required|string|max:255',
//        'message_type_id' => 'required|integer',
    ];
    protected $strings_key = [
//        'content'         => '消息内容',
        //'serviceid' => '业务id',
        //'message_id' => '消息id',
        //'url' => '网页地址',
//        'message_type_id' => '消息类型',
    ];
    protected $strings_val = [
//        'required' => '为必填项',
//        'string'   => '为字符串',
//        'max'      => '最大为:max',
//        'integer'  => '必须为整数',
    ];
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    public function messages() {
        $rules = $this->rules();
        $k_array = $this->strings_key;
        $v_array = $this->strings_val;
        $array = [];
        foreach ($rules as $key => $value) {
            $new_arr = explode('|', $value);//分割成数组
            foreach ($new_arr as $k => $v) {
                $head = strstr($v, ':', true);//截取:之前的字符串
                if ($head) {
                    $v = $head;
                }
                $array[$key . '.' . $v] = $k_array[$key] . $v_array[$v];
            }
        }
        
        return $array;
    }
    
    public function rules() {
        
        return $this->rules;
        
    }
    
    public function wantsJson() {
        return true;
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        if (isset($input['media_ids'])) {
            $input['media_ids'] = implode(',', $input['media_ids']);
        }
        if (isset($input['selectedDepartments'])) {
            $input['r_user_id'] = $this->getInputUserIds($input['selectedDepartments']);
        }
        $input['url'] = "http://";
        $input['message_id'] = 0;
        $input['serviceid'] = 0;
        $input['readed'] = 0;
        $input['sent'] = 0;
        $input['media_ids'] = 1;
        $input['s_user_id'] = 1;
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
        return $receiveUserIds = array_unique(array_merge($accordUserGetId, $accordDepartGetId));
        
    }
    
}
