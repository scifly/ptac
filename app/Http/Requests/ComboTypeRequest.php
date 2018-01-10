<?php
namespace App\Http\Requests;

use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;

class ComboTypeRequest extends FormRequest {

    public function authorize() { return true; }

    public function rules() {

        return [
            'name'      => 'required|string|between:2,60|unique:combo_types,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('id'),
            'amount'    => 'required|integer',
            'discount'  => 'required|integer',
            'school_id' => 'required|integer',
            'months'    => 'required|integer',
            'enabled'   => 'required|boolean',
        ];

    }

    protected function prepareForValidation() {

        $input = $this->all();
        $input['school_id'] = School::schoolId();

        $this->replace($input);

    }

}
