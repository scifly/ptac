<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PqChoiceRequest extends FormRequest {
    
    protected $strings_key = [
        'choice' => '选项内容',
        'pqs_id' => '所属题目',
        'seq_no' => '排序编号',
    ];
    protected $strings_val = [
        'required' => '为必填项',
        'string'   => '为字符串',
        'max'      => '最大为:max',
        'integer'  => '必须为整数',
        'boolean'  => '为0或1',
        'unique'   => '不唯一',
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
        return [
            'choice' => 'required|string|max:255|unique:poll_questionnaire_subject_choices,choice,' .
                $this->input('id') . ',id,' .
                'pqs_id,' . $this->input('pqs_id'),
            'pqs_id' => 'required|integer',
            'seq_no' => 'required|integer|max:3',
        ];
    }
    
    public function wantsJson() { return true; }
    
}
