<?php
namespace App\Http\Requests;

use App\Models\MenuType;
use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest {
    
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
            'name'         => 'required|string|max:30',
            'uri'          => 'nullable|string|max:255',
            'remark'       => 'string|max:255',
            'menu_type_id' => 'required|integer',
            'media_id'     => 'integer',
            'icon_id'      => 'integer',
            'position'     => 'integer',
            'enabled'      => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (!isset($input['position'])) {
            $input['position'] = 0;
        }
        $input['menu_type_id'] = MenuType::whereName('其他')->first()->id;
        
        $this->replace($input);
        
    }
    
}
