<?php
namespace App\Models;

use App\Helpers\Constant;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model};
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
 * @property-read Major $major
 * @property-read Subject $subject
 * @method static Builder|MajorSubject whereCreatedAt($value)
 * @method static Builder|MajorSubject whereId($value)
 * @method static Builder|MajorSubject whereMajorId($value)
 * @method static Builder|MajorSubject whereSubjectId($value)
 * @method static Builder|MajorSubject whereUpdatedAt($value)
 * @method static Builder|MajorSubject newModelQuery()
 * @method static Builder|MajorSubject newQuery()
 * @method static Builder|MajorSubject query()
 * @mixin Eloquent
 */
class MajorSubject extends Model {
    
    protected $table = 'majors_subjects';
    
    protected $fillable = ['major_id', 'subject_id'];
    
    /**
     * 保存专业 & 科目绑定关系
     *
     * @param string $field
     * @param $value
     * @param array $ids
     * @return bool
     * @throws Throwable
     */
    function store(string $field, $value, array $ids) {
    
        try {
            DB::transaction(function () use ($field, $value, $ids) {
                $this->where($field, $value)->delete();
                $records = [];
                foreach ($ids as $id) {
                    $records[] = array_merge(Constant::MS_FIELDS, [
                        $field == 'major_id' ? $value : $id,
                        $field == 'major_id' ? $id : $value,
                        now()->toDateTimeString(),
                        now()->toDateTimeString(),
                    ]);
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}
