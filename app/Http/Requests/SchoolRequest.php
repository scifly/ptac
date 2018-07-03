<?php
namespace App\Http\Requests;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

/**
 * Class SchoolRequest
 * @package App\Http\Requests
 */
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
    
        if (Request::has('ids')) {
            return [
                'ids' => 'required|array',
                'action' => [
                    'required', Rule::in(Constant::BATCH_OPERATIONS)
                ]
            ];
        }
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
        
        if (!Request::has('ids')) {
            $input = $this->all();
            # 保存 - store
            if (Request::method() == 'POST') {
                if (!isset($input['department_id'])) {
                    $input['department_id'] = 0;
                }
                if (!isset($input['menu_id'])) {
                    $input['menu_id'] = 0;
                }
                # 更新 - update
            } else {
                if (Request::has('id')) {
                    $school = School::find(Request::input('id'));
                    $input['department_id'] = $school->department_id;
                    $input['menu_id'] = $school->menu_id;
                }
            }
            if (!isset($input['school_type_id'])) {
                $input['school_type_id'] = School::find($this->schoolId())->school_type_id;
            }
            if (!isset($input['corp_id'])) {
                $user = Auth::user();
                $departmentId = $this->head(Auth::user());
                if ($user->group->name == '企业') {
                    $input['corp_id'] = Corp::whereDepartmentId($departmentId)->first()->id;
                } else {
                    $input['corp_id'] = School::whereDepartmentId($departmentId)->first()->corp_id;
                }
            }
            $this->replace($input);
        }
        
    }
    
}
