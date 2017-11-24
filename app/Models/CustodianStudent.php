<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustodianStudent
 *
 * @property int $id
 * @property int $custodian_id 监护人ID
 * @property int $student_id 学生ID
 * @property string $relationship 关系
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|CustodianStudent whereCreatedAt($value)
 * @method static Builder|CustodianStudent whereCustodianId($value)
 * @method static Builder|CustodianStudent whereId($value)
 * @method static Builder|CustodianStudent whereRelationship($value)
 * @method static Builder|CustodianStudent whereStudentId($value)
 * @method static Builder|CustodianStudent whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $enabled 是否启用
 * @property-read \App\Models\Custodian $custodian
 * @property-read \App\Models\Student $student
 * @method static Builder|CustodianStudent whereEnabled($value)
 * @property string|null $expiration
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustodianStudent whereExpiration($value)
 */
class CustodianStudent extends Model {
    
    protected $table = 'custodians_students';
    
    protected $fillable = [
        'custodian_id', 'student_id', 'relationship',
        'expiration', 'enabled',
    ];
    
    public function custodian() { return $this->belongsTo('App\Models\Custodian'); }
    
    public function student() { return $this->belongsTo('App\Models\Student'); }
    
    public function storeByCustodianId($custodianId, array $studentIds) {
        
        foreach ($studentIds as $studentId => $relationship) {
            $this->create([
                'custodian_id' => $custodianId,
                'student_id'   => $studentId,
                'enabled'      => 1,
                'relationship' => $relationship,
            ]);
        }
        
    }
    
    public function storeByStudentId($studentId, array $custodianIds) {
        
        foreach ($custodianIds as $custodianId => $relationship) {
            $this->create([
                'student_id'   => $studentId,
                'custodian_id' => $custodianId,
                'relationship' => $relationship,
                'enabled'      => 1,
            ]);
        }
        
    }
    
}
