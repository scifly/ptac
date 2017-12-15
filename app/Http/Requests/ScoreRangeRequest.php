<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScoreRangeRequest extends FormRequest {

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
            'name'        => 'required|string|max:60|unique:score_ranges,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
                // . ',' . 'subject_ids,' . $this->input('subject_ids') ,
            'school_id'   => 'required|integer|max:11',
            'subject_ids' => 'required|max:11',
            'start_score' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'end_score'   => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'enabled'     => 'required|boolean',
        ];

    }

    protected function prepareForValidation() {

        $input = $this->all();
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        if (isset($input['subject_ids'])) {
            $input['subject_ids'] = implode(',', $input['subject_ids']);
        }
        $this->replace($input);
    }

}
