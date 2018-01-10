<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WapSiteRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {
        
        return [
            'school_id'  => 'required|integer|unique:wap_sites,school_id,' .
                $this->input('id') . ',id',
            'site_title' => 'required|string|max:255',
            'media_ids'  => 'required|string',
            'enabled'    => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['media_ids'])) {
            $input['media_ids'] = implode(',', $input['media_ids']);
        }
        $this->replace($input);
        
    }
}
