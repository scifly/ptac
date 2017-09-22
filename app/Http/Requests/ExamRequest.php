<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest {
    
    protected $strings_key = [
        'name' => '考试名称',
        'remark' => '备注',
        'exam_type_id' => '考试类型',
        'class_ids' => '班级',
        'subject_ids' => '科目',
        'max_scores' => '科目满分',
        'pass_scores' => '科目及格分数',
        'start_date' => '考试开始日期',
        'end_date' => '考试结束日期',
        'enabled' => '是否启用',
    ];
    protected $strings_val = [
        'required' => '为必填项',
        'string' => '为字符串',
        'max' => '最大为:max',
        'integer' => '必须为整数',
        'date' => '必须为日期',
        'boolean' => '为0或1',
        'unique' => '不唯一',
    
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
            'name' => 'required|string|max:255|unique:exams,name,' .
                $this->input('id') . ',id',
            'remark' => 'required|string|max:255',
            'exam_type_id' => 'required|integer',
            'class_ids' => 'required|string',
            'subject_ids' => 'required|string',
            'max_scores' => 'required|string|max:20',
            'pass_scores' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'enabled' => 'required|boolean'
        ];
        
    }
    
    public function wantsJson() { return true; }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        if (isset($input['class_ids'])) {
            $input['class_ids'] = implode(',', $input['class_ids']);
        }
        if (isset($input['subject_ids'])) {
            $input['subject_ids'] = implode(',', $input['subject_ids']);
        }
        $this->replace($input);
        
    }
    
}
