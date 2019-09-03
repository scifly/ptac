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
        !$this->has('school_id') ?: $input['school_id'] = $this->schoolId();
        array_map(
            function ($id) use (&$input) { 
                !$this->has($id) ?: $input[$id] = explode(',', $input[$id]); 
            }, ['menu_ids', 'tab_ids', 'action_ids']
        );
        $this->replace($input);
        
    }
    
}
