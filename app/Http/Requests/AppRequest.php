<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AppRequest
 * @package App\Http\Requests
 */
class AppRequest extends FormRequest {
    
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
        
        if (in_array($this->method(), ['POST', 'PUT'])) {
            return [
                'name'                 => 'required|string|max:36|unique:apps,name,' .
                    $this->input('id') . ',id,' .
                    'agentid,' . $this->input('agentid') . ',' .
                    'corp_id,' . $this->input('corp_id'),
                'corp_id'              => 'required|integer',
                'agentid'              => 'required|string|max:60',
                'secret'               => 'required|string|max:255',
                'token'                => 'string|max:255',
                'encoding_aes_key'     => 'string|max:255',
                'description'          => 'string|max:255',
                'square_logo_url'      => 'string|max:255',
                'allow_userinfos'      => 'string|max:2048',
                'allow_tags'           => 'string|max:255',
                'allow_partys'         => 'string|max:1024',
                'report_location_flag' => 'boolean',
                'redirect_domain'      => 'string|max:255',
                'isreportenter'        => 'boolean',
                'home_url'             => 'string|max:255',
                'menu'                 => 'string|max:1024',
                'enabled'              => 'required|boolean',
            ];
        }
        
        return [];
    
    }
    
    protected function prepareForValidation() {
        
        if ($this->method() === 'POST') {
            $input = $this->all();
            $input['name'] = $input['name'] ?? uniqid('app');
            $input['description'] = $input['description'] ?? '0';
            $input['report_location_flag'] = $input['reprot_location_flag'] ?? 0;
            $input['square_logo_url'] = $input['square_logo_url'] ?? '0';
            $input['redirect_domain'] = $input['redirect_domain'] ?? '0';
            $input['isreportenter'] = $input['isreportenter'] ?? 0;
            $input['home_url'] = $input['home_url'] ?? '0';
            $input['allow_userinfos'] = $input['allow_userinfos'] ?? '0';
            $input['allow_partys'] = $input['allow_partys'] ?? '0';
            $input['allow_tags'] = $input['allow_tags'] ?? '0';
            $input['menu'] = $input['menu'] ?? '0';
            $input['enabled'] = $input['enabled'] ?? 0;
            
            $this->replace($input);
        }
        
    }
    
}
