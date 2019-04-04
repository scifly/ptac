<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PassageRuleRequest
 * @package App\Http\Requests
 */
class PassageRuleRequest extends FormRequest {
    
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
            'school_id'      => 'required|integer',
            'name'           => 'required|string|between:2,60|unique:passage_rules,name,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'ruleid'         => 'required|integer|between:1,254|unique:passage_rules,ruleid,' .
                $this->input('id') . ',id,' .
                'school_id,' . $this->input('school_id'),
            'start_date'     => 'required|date|before_or_equal:end_date',
            'end_date'       => 'required|date',
            'statuses'       => 'required|string|size:7',
            'tr1'            => 'required|string',
            'tr2'            => 'required|string',
            'tr3'            => 'required|string',
            'related_ruleid' => 'nullable|integer|between:1,254|different:ruleid',
            'enabled'        => 'required|boolean',
            'trs' => [
                'required', function ($attribute, $value, $fail) {
                    if (!$this->tRange($value)) $fail($attribute . ': 设置无效');
                }
            ]
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $dates = explode(' ~ ', $input['daterange']);
        $input['start_date'] = $dates[0];
        $input['end_date'] = $dates[1];
        $statuses = '';
        for ($i = 0; $i < 7; $i++) {
            $statuses .= array_search($i, $input['weekdays']) !== false ? '1' : '0';
        }
        $input['statuses'] = $statuses;
        foreach ($input['trs'] as $key => $tr) {
            $input['tr' . ($key + 1)] = implode(' - ', $tr);
        }
        $this->replace($input);
        
    }
    
    /**
     * 检查通行时段设置的有效性
     *
     * @param $trs
     * @return bool
     */
    private function tRange($trs) {
        
        for ($i = 0; $i < sizeof($trs); $i++) {
            # 通行时段起始时间不得晚于结束时间
            if ($trs[$i][0] >= $trs[$i][1]) return false;
            for ($j = $i + 1; $j < sizeof($trs); $j++) {
                # 当前通行时段不得与其他通行时段重叠
                if ($trs[$i][1] >= $trs[$j][0]) return false;
            }
        }

        return true;
        
    }
    
}
