<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
        if ($input['iscourse'] == 0) {
            $input['educator_id'] = '0';
            $input['subject_id'] = '0';
        }
        if ($input['alertable'] == 0) {
            $input['alert_mins'] = '0';
        }
        if(!isset($input['start'])) {
            $input['start'] = "1970-01-01 00:00:00";
        }
        if(!isset($input['end'])) {
            $input['end'] = "1970-01-01 00:00:00";
        }
        if(!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        $this->replace($input);
    }
}
