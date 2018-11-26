<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class TabRequest
 * @package App\Http\Requests
 */
class TabRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return User::find(Auth::id())->role() == '运营';
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
    
        $rules = [
            'name'      => 'required|string|between:2,255|unique:tabs,name, ' .
                $this->input('id') . ',id',
            'remark'    => 'nullable|string|between:2,255',
            'action_id' => 'required|integer',
            'group_id'  => 'required|integer',
            'icon_id'   => 'nullable|integer',
            'enabled'   => 'required|boolean',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $input = $this->all();
            if (!isset($input['menu_ids'])) {
                $input['menu_ids'] = [];
            }
            $this->replace($input);
        }
        
    }
    
}
