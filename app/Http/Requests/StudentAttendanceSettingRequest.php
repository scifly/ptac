<?php
namespace App\Http\Requests;

use App\Rules\Overlaid;
use App\Rules\StartEnd;
use Illuminate\Foundation\Http\FormRequest;

class StudentAttendanceSettingRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name'         => 'required|string|between:2,60|unique:student_attendance_settings,name,' .
                $this->input('id') . ',id,' .
                'grade_id,' . $this->input('grade_id') . ',' .
                'semester_id,' . $this->input('semester_id'),
            'msg_template' => 'required|string|between:2,255',
            'start' => 'required',
            'end'   => 'required',
            'startend' => [
                'required', new StartEnd(), new Overlaid()
            ]
        ];
    }
    
    public function messages() {
        
        return [
            'name.required'         => '名称不能为空',
            'name.between'          => '名称应该在2~60个字符之间',
            'name.unique'           => '已有该记录',
            'msg_template.required' => '消息模板不能为空',
            'msg_template.between'  => '消息模板应该在2~225个字符之间',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (!isset($input['inorout'])) {
            $input['inorout'] = 0;
        }
        if (!isset($input['ispublic'])) {
            $input['ispublic'] = 0;
        }
        $input['startend'] = [
            $input['start'], $input['end'], 'student', isset($input['id']) ? $input['id'] : null
        ];
        $this->replace($input);
    }
}
