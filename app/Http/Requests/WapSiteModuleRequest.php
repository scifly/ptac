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
            'wap_site_id' => 'required|integer',
            'name'        => 'required|string|max:255|unique:wap_site_modules,name,',
            'media_id'    => 'required|integer',
            'enabled'     => 'required|boolean',
        ];
        
    }
    
}
