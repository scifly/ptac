<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest {
    
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
/*            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('teams')->ignore($this->input('id'))->where(function (Builder $query) {
                    $query->where('name', $this->input('name'));
                    $query->where('school_id', $this->input('school_id'));
                })
            ],*/
            'name' => 'required|string|between:2,255|unique:teams,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'school_id' => 'required|integer',
            'remark' => 'nullable|string|max:255',
            'enabled' => 'required|boolean'
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
        $this->replace($input);
        
    }
    
}
