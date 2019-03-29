<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ExamRequest
 * @package App\Http\Requests
 */
class ExamRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        $rules = [
            'name'         => 'required|string|between:4,40|unique:exams,name,' .
                $this->input('id') . ',id',
            'remark'       => 'required|string|max:255',
            'exam_type_id' => 'required|integer',
            'class_ids'    => 'required|string',
            'subject_ids'  => 'required|string',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'enabled'      => 'required|boolean',
        ];
        
        $this->batchRules($ruels);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!$this->has('ids')) {
            $input = $this->all();
            if (isset($input['class_ids'])) {
                $input['class_ids'] = implode(',', $input['class_ids']);
            }
            if (isset($input['subject_ids'])) {
                $input['subject_ids'] = implode(',', $input['subject_ids']);
            }
            if (!isset($input['max_scores'])) {
                $input['max_scores'] = '150';
            }
            if (!isset($input['pass_scores'])) {
                $input['pass_scores'] = '90';
            }
            $dates = explode(' ~ ', $input['daterange']);
            $input['start_date'] = $dates[0];
            $input['end_date'] = $dates[1];
    
            $this->replace($input);
        }
        
    }
    
}
