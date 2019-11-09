<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class WapRequest
 * @package App\Http\Requests
 */
class WapRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        return [
            'name'      => 'required|string|max:255|unique:waps,name,' .
                $this->input('id') . ',id',
            'media_ids' => 'required|string',
            'enabled'   => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (!empty($input['media_ids'])) {
            $input['media_ids'] = join(',', $input['media_ids']);
        }
        $input['school_id'] = $this->schoolId();
        $this->replace($input);
        
    }
    
}
