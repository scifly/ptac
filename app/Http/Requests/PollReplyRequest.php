<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PollReplyRequest
 * @package App\Http\Requests
 */
class PollReplyRequest extends FormRequest {
    
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
            'user_id'       => 'required|integer',
            'poll_topic_id' => 'required|integer',
            'reply'         => 'required|string',
        ];
        
    }
    
}
