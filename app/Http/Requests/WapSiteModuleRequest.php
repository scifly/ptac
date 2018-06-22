<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WapSiteModuleRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    public function rules() {
        
        return [
            'name'        => 'required|string|max:255|unique:wap_site_modules,name,' .
                $this->input('id') . ',id,' .
                'wap_site_id,' . $this->input('wap_site_id'),
            'wap_site_id' => 'required|integer',
            'media_id'    => 'required|integer',
            'enabled'     => 'required|boolean',
        ];
        
    }
    
}
