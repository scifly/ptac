<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BedRequest
 * @package App\Http\Requests
 */
class BedRequest extends FormRequest {
    
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
            'name'       => 'required|string|max:255|unique:beds,name,' .
                $this->input('id') . ',id,' .
                'room_id,' . $this->input('room_id') . ',' .
                'student_id' . $this->input('student_id'),
            'room_id'    => 'required|integer',
            'student_id' => 'required|integer|unique:beds,student_id,' .
                $this->input('id') . ',id',
            'position'   => 'nullable|boolean',
            'remark'     => 'nullable|string|max:255',
            'enabled'    => 'required|boolean',
        ];
        
    }
    
}
