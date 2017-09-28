<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PqSubjectRequest extends FormRequest {

    protected $strings_key = [
        'subject'      => '题目名称',
        'pq_id'        => '所属问卷',
        'subject_type' => '题目类型',
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
            'subject'      => 'required|string|max:255|unique:poll_questionnaire_subjects,subject,' .
                $this->input('id') . ',id,' .
                'pq_id,' . $this->input('pq_id'),
            'pq_id'        => 'required|integer',
            'subject_type' => 'required|integer',
        ];
    }

    public function wantsJson() { return true; }

}
