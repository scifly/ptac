<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CustodianStudent
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
    public function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 按监护人ID保存记录
     * 
     * @param $custodianId
     * @param array $studentIds
     */
    function storeByCustodianId($custodianId, array $studentIds) {

        foreach ($studentIds as $studentId => $relationship) {
            self::create([
                'custodian_id' => $custodianId,
                'student_id' => $studentId,
                'enabled' => 1,
                'relationship' => $relationship,
            ]);
        }

    }
    
    /**
     * 按学生ID保存记录
     * 
     * @param $studentId
     * @param array $custodianIds
     */
    function storeByStudentId($studentId, array $custodianIds) {

        foreach ($custodianIds as $custodianId => $relationship) {
            self::create([
                'student_id' => $studentId,
                'custodian_id' => $custodianId,
                'relationship' => $relationship,
                'enabled' => 1,
            ]);
        }

    }

}
