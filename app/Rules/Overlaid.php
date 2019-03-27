<?php
namespace App\Rules;

use App\Helpers\ModelTrait;
use App\Models\Semester;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class Overlaid
 * @package App\Rules
 */
class Overlaid implements Rule {
    
    use ModelTrait;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        
        $start = $value[0];
        $end = $value[1];
        $schoolId = $this->schoolId();
        if ($value[3]) {
            $settings = Semester::whereSchoolId($schoolId)
                ->where('id', '<>', $value[3])
                ->where('enabled', 1)
                ->pluck('end_date', 'start_date')->toArray();
        } else {
            $settings = Semester::whereSchoolId($schoolId)
                ->where('start_date', '!=', $start)
                ->Where('end_date', '!=', $end)
                ->where('enabled', 1)
                ->pluck('end_date', 'start_date')->toArray();
        }
        $count = sizeof($settings);
        $starts = array_keys($settings);
        $ends = array_values($settings);
        for ($i = 0; $i < $count; $i++) {
            if ($start >= $starts[$i] && $start <= $ends[$i]) return false;
            if ($end >= $starts[$i] && $end <= $ends[$i]) return false;
            if ($start <= $starts[$i] && $end >= $ends[$i]) return false;
        }
        
        return true;
        
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        
        return '时间段与已有设置有重叠';
        
    }
    
}
