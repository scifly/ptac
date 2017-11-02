<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureLogRequest extends FormRequest {
    
    protected $rules = [
        'procedure_id'  => 'required|integer',
        'initiator_msg' => 'required|string|max:255',
        'media_ids'     => 'array',
    ];
    protected $strings_key = [
        'procedure_id'  => '申请项目',
        'initiator_msg' => '发起留言',
        'media_ids'     => '附件',
    ];
    protected $strings_val = [
        'required' => '为必填项',
        'string'   => '为字符串',
        'max'      => '最大为:max',
        'array'    => '必须为数组',
        'integer'  => '必须为整数',
        'boolean'  => '为0或1',
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
    
    public function wantsJson() { return true; }
}
