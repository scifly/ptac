<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|string|max:36|unique:apps,name,' .
                $this->input('id') . ',id,' .
                'agentid,' . $this->input('agentid') . ',' .
                'url,' . $this->input('url') . ',' .
                'token,' . $this->input('token') . ',' .
                'encodingaeskey,' . $this->input('encodingaeskey'),
            'description' => 'required|string|max:255',
            'agentid' => 'required|integer|max:3',
            'url' => 'required|string|max:255',
            'token' => 'required|string|max:255',
            'encodingaeskey' => 'required|string|max:255',
            'report_location_flag' => 'required|integer',
            'logo_mediaid' => 'required|string|max:255',
            'redirect_domain' => 'required|string|max:255',
            'isreportuser' => 'required|boolean',
            'isreportenter' => 'required|boolean',
            'home_url' => 'required|string|max:255',
            'chat_extension_url' => 'required|string|max:255',
            'menu' => 'required|string|max:1024',
            'enabled' => 'required|boolean'
        ];
    }
    
    public function wantsJson() { return true; }
    
}
