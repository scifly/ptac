<?php
namespace App\Models;

use App\Helpers\Constant;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
     * 按监护人ID保存记录
     *
     * @param $custodianId
     * @param array $studentIds
     * @throws Throwable
     */
    function storeByCustodianId($custodianId, array $studentIds) {
        
        try {
            DB::transaction(function () use ($custodianId, $studentIds) {
                $records = [];
                foreach ($studentIds as $studentId => $relationship) {
                    $records[] = [
                        'custodian_id' => $custodianId,
                        'student_id'   => $studentId,
                        'enabled'      => Constant::ENABLED,
                        'relationship' => $relationship,
                        'created_at'   => now()->toDateTimeString(),
                        'updated_at'   => now()->toDateTimeString(),
                    ];
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 按学生ID保存记录
     *
     * @param $studentId
     * @param array $custodianIds
     * @throws Throwable
     */
    function storeByStudentId($studentId, array $custodianIds) {
        
        try {
            DB::transaction(function () use ($studentId, $custodianIds) {
                $records = [];
                foreach ($custodianIds as $custodianId => $relationship) {
                    $records = [
                        'student_id'   => $studentId,
                        'custodian_id' => $custodianId,
                        'relationship' => $relationship,
                        'enabled'      => Constant::ENABLED,
                    ];
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
