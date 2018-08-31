<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TagRequest
 * @package App\Http\Requests
 */
class TagRequest extends FormRequest {
    
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
            /*'name' => [
                'required', 'string', 'max:255',
                Rule::unique('teams')->ignore($this->input('id'))->where(function (Builder $query) {
                    $query->where('name', $this->input('name'));
                    $query->where('school_id', $this->input('school_id'));
                })
            ],*/
            'name'      => 'required|string|between:2,255|unique:tags,name,' .
                $this->input('id') . ',id',
                // 'school_id,' . $this->input('school_id'),
            'school_id' => 'required|integer',
            'remark'    => 'nullable|string|max:255',
            'enabled'   => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $input['name'] = $input['name'] . '.' . $this->schoolId();
        $this->replace($input);
        
    }
    
}
