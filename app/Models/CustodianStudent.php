<?php
namespace App\Models;

use App\Helpers\Constant;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\CustodianStudent 监护人 & 学生关系
 *
 * @property int $id
 * @property int $custodian_id 监护人ID
 * @property int $student_id 学生ID
 * @property string $relationship 关系
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 是否启用
 * @property string|null $expiration
 * @property-read Custodian $custodian
 * @property-read Student $student
 * @method static Builder|CustodianStudent whereCreatedAt($value)
 * @method static Builder|CustodianStudent whereCustodianId($value)
 * @method static Builder|CustodianStudent whereEnabled($value)
 * @method static Builder|CustodianStudent whereExpiration($value)
 * @method static Builder|CustodianStudent whereId($value)
 * @method static Builder|CustodianStudent whereRelationship($value)
 * @method static Builder|CustodianStudent whereStudentId($value)
 * @method static Builder|CustodianStudent whereUpdatedAt($value)
 * @method static Builder|CustodianStudent newModelQuery()
 * @method static Builder|CustodianStudent newQuery()
 * @method static Builder|CustodianStudent query()
 * @mixin Eloquent
 */
class CustodianStudent extends Model {
    
    protected $table = 'custodians_students';
    
    protected $fillable = [
        'custodian_id', 'student_id', 'relationship',
        'expiration', 'enabled',
    ];
    
    /**
     * 返回所属的监护人对象
     *
     * @return BelongsTo
     */
    function custodian() { return $this->belongsTo('App\Models\Custodian'); }
    
    /**
     * 返回所属的学生对象
     *
     * @return BelongsTo
     */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 保存监护人 & 学生绑定关系
     *
     * @param $value
     * @param array $relationships
     * @param bool $forward
     * @return bool
     * @throws Throwable
     */
    function store($value, array $relationships, bool $forward = true) {
        
        try {
            DB::transaction(function () use ($value, $relationships, $forward) {
                $field = $this->fillable[$forward ? 0 : 1];
                $this->where($field, $value)->delete();
                foreach ($relationships as $id => $relationship) {
                    $records[] = array_combine(Constant::CS_FIELDS, [
                        $forward ? $value : $id,
                        $forward ? $id : $value,
                        $relationship,
                        now()->toDateTimeString(),
                        now()->toDateTimeString(),
                        Constant::ENABLED
                    ]);
                }
                $this->insert($records ?? []);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
