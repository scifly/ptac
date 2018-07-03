<?php
namespace App\Policies;
/**
 * Class ConsumptionStat
 * @package App\Policies
 */
class ConsumptionStat {

    public $rangeId, $studentId, $classId, $gradeId, $dateRange;
    
    /**
     * ConsumptionStat constructor.
     * @param array $conditions
     */
    public function __construct(array $conditions) {
        
        $this->rangeId = $conditions['range_id'];
        $this->studentId = $conditions['student_id'];
        $this->classId = $conditions['class_id'];
        $this->gradeId = $conditions['grade_id'];
        $this->dateRange = $conditions['date_range'];
        
    }
    
    
}
