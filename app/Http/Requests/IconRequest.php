<?php
namespace App\Http\Requests;

use Html;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class IconRequest
 * @package App\Http\Requests
 */
class IconRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        
        return $this->user()->role() == '运营';
        
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'name'    => 'required|string|max:60|unique:icons,name,'
                . $this->input('id') . ',id',
            'remark'  => 'required|string|max:255',
            'enabled' => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['remark'] = Html::tag('i', '', ['class' => $input['name']]);
        $this->replace($input);
        
    }
    
}
