<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class CorpRequest
 * @package App\Http\Requests
 */
class CorpRequest extends FormRequest {

    use ModelTrait;
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
            'name'                => 'required|string|between:3,120|unique:corps,name,' .
                $this->input('id') . ',id,' .
                'company_id,' . $this->input('company_id'),
            'company_id'          => 'required|integer',
            'acronym'             => 'required|string|between:3,20|unique:corps,acronym,' .
                $this->input('id') . ',id',
            'department_id'       => 'required|integer',
            'menu_id'             => 'required|integer',
            'corpid'              => 'required|string|max:18',
            'contact_sync_secret' => 'required|string|max:43',
            'encoding_aes_key'    => 'required|string',
            'token'               => 'required|string',
            'departmentid'        => 'required|integer',
            'mchid'               => 'nullable|string|max:10',
            'apikey'              => 'nullable|string|max:32',
            'enabled'             => 'required|boolean'
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (!isset($input['department_id'])) {
            $input['department_id'] = 0;
        }
        if (!isset($input['menu_id'])) {
            $input['menu_id'] = 0;
        }
        if (!isset($input['company_id'])) {
            $user = User::find(Auth::id());
            $departmentId = $this->head($user);
            $input['company_id'] = Corp::whereDepartmentId($departmentId)->first()->company_id;
        }
        if (empty($input['encoding_aes_key'])) {
            $input['encoding_aes_key'] = '0';
        }
        if (empty($input['token'])) {
            $input['token'] = '0';
        }
        if (empty($input['departmentid'])) {
            $input['departmentid'] = 1;
        }
        if (empty($input['mchid'])) {
            $input['mchid'] = '0';
        }
        if (empty($input['apikey'])) {
            $input['apikey'] = '0';
        }
        
        $this->replace($input);
        
    }
    
}
