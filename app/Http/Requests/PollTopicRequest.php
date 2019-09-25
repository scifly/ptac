<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PollTopicRequest
 * @package App\Http\Requests
 */
class PollTopicRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /** @return array */
    public function rules() {
        
        return [
            'topic'    => 'required|string|max:255|unique:poll_topics,topic,' .
                $this->input('id') . ',id,' .
                'poll_id,' . $this->input('poll_id'),
            'poll_id'  => 'required|integer',
            'category' => 'required|integer',
            'content'  => 'nullable|string',
            'enabled'  => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $option = $input['category'] ? $input['option'] : [];
        $input['content'] = json_encode(array_filter($option), JSON_UNESCAPED_UNICODE);
        $this->replace($input);
        
    }
    
}
