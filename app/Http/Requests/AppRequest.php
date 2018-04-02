<?php
namespace App\Http\Requests;

use App\Models\Corp;
use Illuminate\Foundation\Http\FormRequest;

class AppRequest extends FormRequest {
    
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
            'name'                 => 'required|string|max:36|unique:apps,name,' .
                $this->input('id') . ',id,' .
                'agentid,' . $this->input('agentid') . ',' .
                'corp_id,' . $this->input('corp_id'),
            'description'          => 'required|string|max:255',
            'agentid'              => 'required|string|max:60',
            'report_location_flag' => 'required|boolean',
            'redirect_domain'      => 'required|string|max:255',
            'isreportenter'        => 'required|boolean',
            'home_url'             => 'required|string|max:255',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $corp = new Corp();
        $input['corp_id'] = $corp->corpId();
        $this->replace($input);
        
    }
    
}
