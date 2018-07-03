<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GroupRequest
 * @package App\Http\Requests
 */
class GroupRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
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
            'name'       => 'required|string|between:2,255|unique:groups,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'school_id'  => 'required|integer',
            'remark'     => 'required|string|between:2,20',
            'menu_ids'   => 'required|array',
            'tab_ids'    => 'required|array',
            'action_ids' => 'required|array',
            'enabled'    => 'required|integer',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['menu_ids'])) {
            $input['menu_ids'] = explode(',', $input['menu_ids']);
        }
        if (isset($input['tab_ids'])) {
            $input['tab_ids'] = explode(',', $input['tab_ids']);
        }
        if (isset($input['action_ids'])) {
            $input['action_ids'] = explode(',', $input['action_ids']);
        }
        if (!isset($input['school_id'])) {
            $input['school_id'] = $this->schoolId();
        }
        $this->replace($input);
        
    }
    
}
