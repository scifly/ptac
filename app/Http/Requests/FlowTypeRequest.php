<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FlowTypeRequest
 * @package App\Http\Requests
 */
class FlowTypeRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize() { return true; }
    
    /** @return array */
    public function rules() {
        
        return [
            'name'      => 'required|string|max:60|unique:flow_types,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'school_id' => 'required|integer',
            'steps'     => 'required|string',
            'remark'    => 'nullable|string|max:255',
            'enabled'   => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $names = $input['names'];
        $educatorIds = $input['educator_ids'];
        for ($i = 0; $i < sizeof($names); $i++) {
            if (empty($names[$i]) || empty($educatorIds[$i])) continue;
            $steps[] = [
                'name' => $names[$i],
                'ids'  => $educatorIds[$i],
            ];
        }
        $input['steps'] = json_encode(
            $steps ?? [],
            JSON_UNESCAPED_UNICODE
        );
        $this->replace($input);
        
    }
    
}
