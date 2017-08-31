<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EducatorAttendanceSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    protected function prepareForValidation() {

        $input = $this->all();
        dd($input);
        if (isset($input['inorout']) && $input['inorout'] === 'on') {
            $input['inorout'] = 1;
        }
        if (!isset($input['inorout'])) {
            $input['inorout'] = 0;
        }

        $this->replace($input);
    }
}
