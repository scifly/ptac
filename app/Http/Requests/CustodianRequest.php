<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustodianRequest extends FormRequest {
    
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
//            'user_id' => 'required|integer|unique:custodians,user_id,' .
//                $this->input('id') . ',id',
//            'expiry' => 'required|datetime'
        ];
        
    }
    
    protected function prepareForValidation() {

        $input = $this->all();
        dd($input);
        if (isset($input['user']['enabled']) && $input['user']['enabled'] === 'on') {
            $input['user']['enabled'] = 1;
        }
        if (!isset($input['user']['enabled'])) {
            $input['user']['enabled'] = 0;
        }
        $this->replace($input);
    }
    
}
