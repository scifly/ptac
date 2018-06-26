<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'acronym'             => 'required|string|between:3,20|unique:corps,acronym,' .
                $this->input('id') . ',id',
            'department_id'       => 'required|integer',
            'menu_id'             => 'required|integer',
            'corpid'              => 'required|string|max:18',
            'contact_sync_secret' => 'required|string|max:43',
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
            $user = Auth::user();
            $departmentId = $this->head($user);
            $input['company_id'] = Corp::whereDepartmentId($departmentId)->first()->company_id;
        }
        
        $this->replace($input);
        
    }
    
}
