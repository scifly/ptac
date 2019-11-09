<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'name'      => 'required|string|between:2,32|unique:tags,name,' .
                $this->input('id') . ',id,' .
                'user_id,' . $this->input('user_id'),
            'school_id' => 'required|integer',
            'user_id'   => 'required|integer',
            'remark'    => 'nullable|string|max:255',
            'enabled'   => 'required|boolean',
            'synced'    => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $input['user_id'] = Auth::id();
        $input['synced'] = 0;
        $deptIds = $userIds = [];
        $targetIds = explode(',', $input['selected-node-ids'] ?? '');
        foreach ($targetIds as $targetId) {
            $paths = explode('-', $targetId);
            isset($paths[2])
                ? $userIds[] = $paths[2]
                : $deptIds[] = $targetId;
        }
        $input['user_ids'] = array_unique($userIds);
        $input['dept_ids'] = array_unique($deptIds);
        $this->replace($input);
        
    }
    
}
