<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{

    protected $rules = [
        'content' => 'required|string|max:255',
        'serviceid' => 'required|string|max:255',
        'message_id' => 'required|integer',
        'url' => 'required|string|max:255',
        'media_ids' => 'required|string',
        'user_id' => 'required|integer',
        'user_ids' => 'required|string',
        'message_type_id' => 'required|integer',
    ];
    protected $strings_key = [
        'content' => '消息内容',
        'serviceid' => '业务id',
        'message_id' => '消息id',
        'url' => '网页地址',
        'media_ids' => '轮播图',
        'user_id' => '发送者用户',
        'user_ids' => '接收用户',
        'message_type_id' => '消息类型',
    ];
    protected $strings_val = [
        'required'=> '为必填项',
        'string'=> '为字符串',
        'max'=> '最大为:max',
        'integer'=> '必须为整数',
    ];


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return $this->rules;

    }

    public function messages() {
        $rules = $this->rules();
        $k_array = $this->strings_key;
        $v_array = $this->strings_val;
        $array = [];
        foreach ($rules as $key => $value) {
            $new_arr = explode('|', $value);//分割成数组
            foreach ($new_arr as $k => $v) {
                $head = strstr($v,':',true);//截取:之前的字符串
                if ($head) {$v = $head;}
                $array[$key.'.'.$v] = $k_array[$key].$v_array[$v];
            }
        }

        return $array;
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
        if (isset($input['media_ids'])) {
            $input['media_ids'] = implode(',', $input['media_ids']);
        }
        if (isset($input['user_ids'])) {
            $input['user_ids'] = implode(',', $input['user_ids']);
        }
        if (!isset($input['read_count'])) {
            $input['read_count'] = 0;
        }
        if (!isset($input['recipient_count'])) {
            $input['recipient_count'] = 0;
        }
        if (!isset($input['received_count'])) {
            $input['received_count'] = 0;
        }
        $this->replace($input);
    }
}
