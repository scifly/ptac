<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceMachineRequest extends FormRequest {
    
    protected $strings_key = [
        'name' => '考勤机名称',
        'location' => '位置',
        'school_id' => '学校',
        'machineid' => '设备id',
        'enabled' => '是否启用'
    ];
    protected $strings_val = [
        'required' => '为必填项',
        'string' => '为字符串',
        'integer' => '为整数',
        'max' => '最大为:max',
        'boolean' => '为0或1',
    ];
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function messages() {
        
        $rules = $this->rules();
        $k_array = $this->strings_key;
        $v_array = $this->strings_val;
        $array = array();
        
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
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'name' => 'required|string|between:2,60|unique:attendance_machines,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id') . ',' .
                'machineid,' . $this->input('machineid'),
            'location' => 'required|string|between:2,255',
            'school_id' => 'required|integer',
            'machineid' => 'required|string|betweeen:2,20',
            'enabled' => 'required|boolean'
        ];
        
    }
    
    public function wantsJson() { return true; }
    
}
