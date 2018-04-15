<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest {
    
    public function authorize() { return true; }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'name'       => 'required|string|between:2,255|unique:groups,name,' .
                $this->input('id') . ',id',
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
        if (isset($input['tabs'])) {
            $tabIds = null;
            foreach ($input['tabs'] as $key => $value) {
                $tabIds[] = $key;
            }
            $input['tab_ids'] = $tabIds;
        }
        $actionIds = [];
        if (isset($input['actions'])) {
            foreach ($input['actions'] as $key => $value) {
                $actionIds[] = $key;
            }
            $input['action_ids'] = $actionIds;
        }
        if (isset($input['menu_ids'])) {
            $input['menu_ids'] = explode(',', $input['menu_ids']);
        }
        $this->replace($input);
        
    }
    
}
