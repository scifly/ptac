<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MajorSubject
 *
 * @property int $id
 * @property int $major_id 专业ID
 * @property int $subject_id 科目ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|MajorSubject whereCreatedAt($value)
 * @method static Builder|MajorSubject whereId($value)
 * @method static Builder|MajorSubject whereMajorId($value)
 * @method static Builder|MajorSubject whereSubjectId($value)
 * @method static Builder|MajorSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Major $major
 * @property-read Subject $subject
 */
class MajorSubject extends Model {
    
    protected $table = 'majors_subjects';
    
    protected $fillable = ['major_id', 'subject_id'];
    
    public function storeByMajorId($majorId, array $subjectIds) {
        foreach ($subjectIds as $subjectId) {
            $this->create([
                'major_id'   => $majorId,
                'subject_id' => $subjectId,
            ]);
        }
        
    }
    
    public function storeBySubjectId($subjectId, $majorIds) {
        foreach ($majorIds as $majorId) {
            $this->create([
                'major_id'   => $majorId,
                'subject_id' => $subjectId,
            ]);
        }
        
    }
    
}
