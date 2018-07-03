<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class StartEnd
 * @package App\Rules
 */
class StartEnd implements Rule {
    
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
        
        return $start <= $end;
        
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        
        return '开始时间不得大于等于结束时间';
        
    }
    
}
