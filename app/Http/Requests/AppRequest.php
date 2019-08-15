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
        
        $rules = [
            'name'        => 'required|string|max:36|unique:apps,name,' .
                $this->input('id') . ',id,' .
                'appid,' . $this->input('appid') . ',' .
                'corp_id,' . $this->input('corp_id'),
            'corp_id'     => 'required|integer',
            'category'    => 'required|integer|between:1,3',
            'appsecret'   => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
        switch ($this->input('category')) {
            case 1:
                $extra = [
                    'appid' => 'required|string|max:60',
                ];
                break;
            case 2:
                $extra = [
                    'appid'            => 'required|string|max:60',
                    'token'            => 'required|string|max:255',
                    'encoding_aes_key' => 'required|string|max:255',
                ];
                break;
            default:
                $extra = [
                    'url'              => 'required|url',
                    'token'            => 'required|string|max:255',
                    'encoding_aes_key' => 'required|string|max:255',
                ];
                break;
        }
        
        return array_merge($rules, $extra);
        
    }
    
    protected function prepareForValidation() {

        $input = $this->input();
        $input['enabled'] = $input['enabled'] ?? 1;
        $input['category'] != 3 ?: $input['name'] = '通讯录同步';
        
        $this->replace($input);
        
    }
    
}
