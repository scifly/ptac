<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ArticleRequest
 * @package App\Http\Requests
 */
class ArticleRequest extends FormRequest {
    
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
            'column_id' => 'required|integer',
            'name'      => 'required|string|max:120',
            'summary'   => 'required|string|max:255',
            'content'   => 'required|string',
            'media_ids' => 'required|string',
            'enabled'   => 'required|boolean',
        ];
        
    }
    
    /**
     * @return bool
     */
    public function wantsJson() { return true; }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (isset($input['media_ids'])) {
            $input['thumbnail_media_id'] = $input['media_ids'][0];
            $input['media_ids'] = join(',', $input['media_ids']);
        }
        $this->replace($input);
    }
    
}
