<?php

namespace App\Rules;

use App\Models\EducatorAttendanceSetting;
use App\Models\Grade;
use App\Models\School;
use App\Models\Score;
use App\Models\Semester;
use App\Models\StudentAttendanceSetting;
use Illuminate\Contracts\Validation\Rule;

class Overlaid implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $start = $value[0];
        $end = $value[1];
        switch ($value[2]) {
            case 'educator':
                if ($value[3]) {
                    $settings = EducatorAttendanceSetting::where('id', '<>', $value[3])
                        ->where('enabled','1')
                        ->pluck('end', 'start')->toArray();
                } else {
                    $settings = EducatorAttendanceSetting::pluck('end', 'start')
                        ->where('enabled','1')
                        ->toArray();
                }
                break;
            case 'student':
                $gradeIds = [];
                $schoolId = School::schoolId();
                $grade = Grade::whereSchoolId($schoolId)->get();
                foreach ($grade as $g){
                    $gradeIds[] = $g->id;
                }
                if($value[3]){
                    $settings = StudentAttendanceSetting::where('id','<>', $value[3])
                        ->whereIn('grade_id',$gradeIds)
                        ->where('day',$value[4])
                        ->pluck('end', 'start')->toArray();
                } else {
                    $settings = StudentAttendanceSetting::whereIn('grade_id',$gradeIds)
                        ->where('day',$value[4])
                        ->pluck('end', 'start')->toArray();
                }
                break;
            case 'semester':
                $schoolId = School::schoolId();
                if($value[3]){
                    $settings = Semester::whereSchoolId($schoolId)
                        ->where('id','<>', $value[3])
                        ->where('enabled',1)
                        ->pluck('end_date','start_date')->toArray();
                }else{
                    $settings =Semester::whereSchoolId($schoolId)
                        ->where('end_date','!=',$start)
                        ->orWhere('start_date','!=',$end)
                        ->where('enabled',1)
                        ->pluck('end_date','start_date')->toArray();
                }
                break;
            default :
                return false;
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
    public function message()
    {
        return '时间段与已有设置有重叠';
    }
}
