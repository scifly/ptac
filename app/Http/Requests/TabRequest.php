<?php
namespace App\Http\Requests;

use App\Helpers\Constant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

/**
 * Class TabRequest
 * @package App\Http\Requests
 */
class TabRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return Auth::user()->group->name == '运营';
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
    
        if (Request::has('ids')) {
            return [
                'ids' => 'required|array',
                'action' => [
                    'required', Rule::in(Constant::BATCH_OPERATIONS)
                ]
            ];
        }
        
        return [
            'name'      => 'required|string|between:2,255|unique:tabs,name, ' .
                $this->input('id') . ',id',
            'remark'    => 'nullable|string|between:2,255',
            'action_id' => 'required|integer',
            'group_id'  => 'required|integer',
            'icon_id'   => 'nullable|integer',
            'enabled'   => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        if (!$this->attributes->has('ids')) {
            $input = $this->all();
            if (!isset($input['menu_ids'])) {
                $input['menu_ids'] = [];
            }
            $this->replace($input);
        }
        
    }
    
}
