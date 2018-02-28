<?php

namespace App\Http\Requests;


use App\Rules\Mobiles;
use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest {

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
            'card_number' => 'required|alphanum|between:2,32|unique:students,card_number,'
            . $this->input('user_id') . ',user_id',
            'user.realname' => 'required|string',
            'user.gender' => 'required|boolean',
            'user.email' => 'nullable|email|unique:users,email,' .
                $this->input('user_id') . ',id',
            'mobile.*' => ['required', new Mobiles()] ,
            'student_number' => 'required|alphanum|between:2,32|unique:students,student_number,'
                . $this->input('user_id') . ',user_id',
            'birthday' => 'required',
        ];

    }

    protected function prepareForValidation() {

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

        $this->replace($input);

    }

}
