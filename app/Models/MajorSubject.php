<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\MajorSubject 专业与科目关系
 *
 * @property int $id
 * @property int $major_id 专业ID
 * @property int $subject_id 科目ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|MajorSubject whereCreatedAt($value)
 * @method static Builder|MajorSubject whereId($value)
 * @method static Builder|MajorSubject whereMajorId($value)
 * @method static Builder|MajorSubject whereSubjectId($value)
 * @method static Builder|MajorSubject whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Major $major
 * @property-read Subject $subject
 */
class MajorSubject extends Model {
    
    protected $table = 'majors_subjects';
    
    protected $fillable = ['major_id', 'subject_id'];
    
    /**
     * 根据专业id保存记录
     *
     * @param $majorId
     * @param array $subjectIds
     * @throws Throwable
     */
    function storeByMajorId($majorId, array $subjectIds) {
        
        try {
            DB::transaction(function () use ($majorId, $subjectIds) {
                $this->whereMajorId($majorId)->delete();
                $records = [];
                foreach ($subjectIds as $subjectId) {
                    $records[] = [
                        'major_id'   => $majorId,
                        'subject_id' => $subjectId,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ];
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 根据科目id保存记录
     *
     * @param $subjectId
     * @param $majorIds
     * @throws Throwable
     */
    function storeBySubjectId($subjectId, $majorIds) {
        
        try {
            DB::transaction(function () use ($subjectId, $majorIds) {
                $this->whereSubjectId($subjectId)->delete();
                $records = [];
                foreach ($majorIds as $majorId) {
                    $records[] = [
                        'major_id'   => $majorId,
                        'subject_id' => $subjectId,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ];
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
