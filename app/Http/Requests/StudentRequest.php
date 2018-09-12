<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

/**
 * Class StudentRequest
 * @package App\Http\Requests
 */
class StudentRequest extends FormRequest {
    
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
    
        $rules = [
            'card_number'    => 'required|alphanum|between:2,32|unique:students,card_number,' .
                $this->input('user_id') . ',user_id',
            'user.realname'  => 'required|string',
            'user.gender'    => 'required|boolean',
            'user.email'     => ['nullable', 'email', new Email],
            'mobile.*'       => ['nullable', new Mobile],
            'remark'         => 'required|string',
            'student_number' => 'required|alphanum|between:2,32|unique:students,student_number,' .
                $this->input('user_id') . ',user_id',
            'birthday'       => 'required',
        ];
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $input = $this->all();
            if (isset($input['mobile'])) {
                $index = $input['mobile']['isdefault'];
                unset($input['mobile']['isdefault']);
                foreach ($input['mobile'] as $i => $m) {
                    if ($i == $index) {
                        $input['mobile'][$i]['isdefault'] = 1;
                    } else {
                        $input['mobile'][$i]['isdefault'] = 0;
                    }
                    if (!isset($m['enabled'])) {
                        $input['mobile'][$i]['enabled'] = 0;
                    } else {
                        $input['mobile'][$i]['enabled'] = 1;
                    }
                }
            }
            if (!isset($input['remark'])) {
                $input['remark'] = 'student';
            }
            $this->replace($input);
        }
        
    }
    
}
