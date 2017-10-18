<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
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
    
    public function wantsJson() { return true; }
    protected function prepareForValidation() {
    
        $input = $this->all();
        if (isset($input['report_location_flag']) && $input['report_location_flag'] === 'on') {
            $input['report_location_flag'] = 1;
        }
        if (!isset($input['report_location_flag'])) {
            $input['report_location_flag'] = 0;
        }
        if (isset($input['isreportenter']) && $input['isreportenter'] === 'on') {
            $input['isreportenter'] = 1;
        }
        if (!isset($input['isreportenter'])) {
            $input['isreportenter'] = 0;
        }
        $this->replace($input);
    }
    
}
