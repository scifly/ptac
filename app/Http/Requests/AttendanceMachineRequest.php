<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceMachineRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    // public function messages() {
    //
    //     // $rules = $this->rules();
    //     // $k_array = $this->strings_key;
    //     // $v_array = $this->strings_val;
    //     // $array = [];
    //     // foreach ($rules as $key => $value) {
    //     //     $new_arr = explode('|', $value);//分割成数组
    //     //     foreach ($new_arr as $k => $v) {
    //     //         $head = strstr($v, ':', true);//截取:之前的字符串
    //     //         if ($head) {
    //     //             $v = $head;
    //     //         }
    //     //         $array[$key . '.' . $v] = $k_array[$key] . $v_array[$v];
    //     //     }
    //     // }
    //     // return $array;
    // }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'name'      => 'required|string|between:2,60|unique:attendance_machines,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id') . ',' .
                'machineid,' . $this->input('machineid'),
            'location'  => 'required|string|between:2,255',
            'machineid' => 'required|string|between:2,20',
            'school_id' => 'required|integer',
            'enabled'   => 'required|boolean',
        ];
        
    }


    protected function prepareForValidation() {

        $input = $this->all();

        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        $this->replace($input);

    }
    public function wantsJson() { return true; }
    
}
