<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SchoolRequest extends FormRequest {
    
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
            'name'           => 'required|string|between:6,255|unique:schools,name,' .
                $this->input('id') . ',id',
            'address'        => 'required|string|between:6,255',
            'signature'      => 'required|string|between:2,7',
            'department_id'  => 'required|integer',
            'corp_id'        => 'required|integer',
            'menu_id'        => 'required|integer',
            'school_type_id' => 'required|integer',
            'enabled'        => 'required|boolean',
        ];

    }

    protected function prepareForValidation() {

        $input = $this->all();
        if (Request::method() == 'POST') {
            if (!isset($input['department_id'])) {
                $input['department_id'] = 0;
            }
            if (!isset($input['menu_id'])) {
                $input['menu_id'] = 0;
            }
        } else {
            $school = School::find(Request::input('id'));
            $input['department_id'] = $school->department_id;
            $input['menu_id'] = $school->menu_id;
        }
        
        if (!isset($input['school_type_id'])) {
            $input['school_type_id'] = School::find($this->schoolId())->school_type_id;
        }
        if (!isset($input['corp_id'])) {
            $input['corp_id'] = Corp::whereDepartmentId(Auth::user()
                ->topDeptId())->first()->id;
        }
        $this->replace($input);

    }

}
