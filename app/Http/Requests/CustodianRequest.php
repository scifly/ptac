<?php
namespace App\Http\Requests;

use App\Helpers\Constant;
use App\Models\Group;
use App\Rules\Email;
use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

/**
 * Class CustodianRequest
 * @package App\Http\Requests
 */
class CustodianRequest extends FormRequest {
    
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
    
        if (Request::has('ids')) {
            return [
                'ids' => 'required|array',
                'action' => [
                    'required', Rule::in(Constant::BATCH_OPERATIONS)
                ]
            ];
        }
        return [
            'user.realname' => 'required|string',
            'user.gender'   => 'required|boolean',
            'user.group_id' => 'required|integer',
            'user.email'    => ['nullable', 'email', new Email],
            'mobile.*'      => ['required', new Mobile],
            'student_ids'   => 'required',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        if (!Request::has('ids')) {
            $input = $this->all();
            if (isset($input['mobile'])) {
                $defaultIndex = $input['mobile']['isdefault'];
                unset($input['mobile']['isdefault']);
                foreach ($input['mobile'] as $index => $mobile) {
                    $input['mobile'][$index]['user_id'] = isset($input['user_id']) ? $input['user_id'] : 0;
                    if ($index == $defaultIndex) {
                        $input['mobile'][$index]['isdefault'] = 1;
                    } else {
                        $input['mobile'][$index]['isdefault'] = 0;
                    }
                    if (!isset($mobile['enabled'])) {
                        $input['mobile'][$index]['enabled'] = 0;
                    } else {
                        $input['mobile'][$index]['enabled'] = 1;
                    }
                }
            }
            $input['user']['group_id'] = Group::whereName('监护人')->first()->id;
            $this->replace($input);
        }
    }
    
}
