<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EducatorAttendanceSettingRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name'  => 'required|string|between:2,60|unique:educator_attendance_settings,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'start' => 'required',
            'end'   => 'required',
        ];
    }

    public function messages() {

        return [
            'name.required'  => '名称不能为空',
            'name.between'   => '名称应该在2~60个字符之间',
            'name.unique'    => '已有该记录',
            'start.required' => '起始时间不能为空',
            'end.required'   => '结束时间不能为空',
        ];

    }

    public function wantsJson() { return true; }

    protected function prepareForValidation() {

        $input = $this->all();
        if (isset($input['inorout']) && $input['inorout'] === 'on') {
            $input['inorout'] = 1;
        }
        if (!isset($input['inorout'])) {
            $input['inorout'] = 0;
        }
        $this->replace($input);

    }

}
