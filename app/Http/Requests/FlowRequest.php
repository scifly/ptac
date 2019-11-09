<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Flow;
use App\Models\FlowType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class FlowTypeRequest
 * @package App\Http\Requests
 */
class FlowRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /** @return array */
    public function rules() {
        
        return [
            'flow_type_id' => 'required|integer',
            'user_id'      => 'required|integer',
            'media_ids'    => 'nullable|integer',
            'logs'         => 'required|string',
            'step'         => 'required|integer',
            'status'       => 'nullable|integer',
            'enabled'      => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        if (Request::has('ids')) {
            $input['ids'] = Flow::whereUserId(Auth::id())
                ->pluck('id')->intersect($input['ids'])
                ->toArray();
        } else {
            $isPost = $this->method() == 'POST';
            $logs = json_decode(
                $isPost
                    ? FlowType::find($input['flow_type_id'])->steps
                    : Flow::find($input['id'])->logs,
                true
            );
            if ($isPost) {
                $logs[1]['status'] = 0;
                $input['user_id'] = Auth::id();
            } else {
                $step = $input['step'];
                $status = $input['status'];
                $logs[$step]['status'] = $status;
                $logs[$step]['userId'] = Auth::id();
                if ($status == 1 && sizeof($logs) > $step) {
                    $logs[$step + 1]['status'] = 0;
                }
            }
            $input['logs'] = json_encode($logs, JSON_UNESCAPED_UNICODE);
        }
        $this->replace($input);
        
    }
    
}
