<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class WapSiteModuleRequest extends FormRequest
{

    protected $rules = [
        'wap_site_id' => 'required|integer',
        'name' => 'required|string|max:255',
        'media_ids' => 'required|array',
        'enabled' => 'required|boolean'
    ];
    protected $strings_key = [
        'name' => '名称',
        'wap_site_id' => '所属网站',
        'media_ids' => '轮播图',
        'enabled' => '是否启用'
    ];
    protected $strings_val = [
        'required'=> '为必填项',
        'string'=> '为字符串',
        'max'=> '最大为:max',
        'integer'=> '必须为整数',
        'array'=> '必须为数组',
        'boolean'=> '为0或1',
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

}
