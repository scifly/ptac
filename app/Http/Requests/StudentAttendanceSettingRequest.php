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
    public function authorize() { return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'name' => 'required|string|between:2,60|unique:student_attendance_settings,name,' .
                $this->input('id') . ',id,' .
                'grade_id,' . $this->input('grade_id') . ',' .
                'day,' . $this->input('day') . ',' .
                'semester_id,' . $this->input('semester_id'),
            'msg_template' => 'required|string|between:2,255',
            'start' => 'required',
            'end' => 'required',

            'startend' => [
                'required', new StartEnd(), new Overlaid()
            ]
        ];
        
    }

    protected function prepareForValidation() {

        $input = $this->all();
        $input['startend'] = [
            $input['start'], $input['end'], 'student', $input['id'] ?? null , $input['day']
        ];
        
        $this->replace($input);
        
    }
}
