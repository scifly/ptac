<?php
namespace App\Http\Requests;

use App\Helpers\ModelTrait;
use App\Models\Semester;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SemesterRequest
 * @package App\Http\Requests
 */
class SemesterRequest extends FormRequest {
    
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
            'school_id'  => 'required|integer',
            'name'       => 'required|string',
            'remark'     => 'nullable|string',
            'start_date' => 'required|date|before:end_date',
            'end_date'   => 'required|date|after:start_date',
            'enabled'    => 'required|boolean',
            'startend'   => [
                'required', function ($attribute, $value, $fail) {
                    if (!$this->dRange($value)) $fail($attribute . ': 日期范围无效');
                },
            ],
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['school_id'] = $this->schoolId();
        $input['startend'] = [
            $input['start_date'],
            $input['end_date'],
            $input['id'] ?? null,
        ];
        
        $this->replace($input);
        
    }
    
    /**
     * 检查学期日期范围的有效性
     *
     * @param $value
     * @return bool
     */
    private function dRange($value) {
        
        list($start, $end, $id) = $value;
        $conditions = array_merge(
            [
                ['school_id', '=', $this->schoolId()],
                ['enabled', '=', 1],
            ], $id
            ? [
                ['id', '<>', $id],
            ]
            : [
                ['start_date', '<>', $start],
                ['end_date', '<>', $end],
            ]
        );
        $settings = Semester::where($conditions)->get()
            ->pluck('end_date', 'start_date')
            ->toArray();
        $count = sizeof($settings);
        $starts = array_keys($settings);
        $ends = array_values($settings);
        for ($i = 0; $i < $count; $i++) {
            if (
                ($start >= $starts[$i] && $start <= $ends[$i]) ||
                ($end >= $starts[$i] && $end <= $ends[$i]) ||
                ($start <= $starts[$i] && $end >= $ends[$i])
            ) return false;
        }
        
        return true;
        
    }
    
}
