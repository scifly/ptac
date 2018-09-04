<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TagRequest
 * @package App\Http\Requests
 */
class TagRequest extends FormRequest {
    
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
            /*'name' => [
                'required', 'string', 'max:255',
                Rule::unique('teams')->ignore($this->input('id'))->where(function (Builder $query) {
                    $query->where('name', $this->input('name'));
                    $query->where('school_id', $this->input('school_id'));
                })
            ],*/
            'name'           => 'required|string|between:2,32|unique:tags,name,' .
                $this->input('id') . ',id',
            // 'school_id,' . $this->input('school_id'),
            'school_id'      => 'required|integer',
            'remark'         => 'nullable|string|max:255',
            'enabled'        => 'required|boolean',
            'synced'         => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $input['name'] = $input['name'] . '.' . $this->schoolId();
        $input['synced'] = 0;
        if (isset($input['selected-node-ids'])) {
            $deptIds = $userIds = [];
            $targetIds = explode(',', $input['selected-node-ids']);
            foreach ($targetIds as $targetId) {
                $paths = explode('-', $targetId);
                if (isset($paths[2])) {
                    $userIds[] = $paths[2];
                } else {
                    $deptIds[] = $targetId;
                }
            }
            $input['user_ids'] = array_unique($userIds);
            $input['dept_ids'] = array_unique($deptIds);
        }
        $this->replace($input);
        
    }
    
}
