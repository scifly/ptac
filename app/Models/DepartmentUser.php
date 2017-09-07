<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DepartmentUser
 *
 * @property int $id
 * @property int $department_id 部门ID
 * @property int $user_id 用户ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|DepartmentUser whereCreatedAt($value)
 * @method static Builder|DepartmentUser whereDepartmentId($value)
 * @method static Builder|DepartmentUser whereEnabled($value)
 * @method static Builder|DepartmentUser whereId($value)
 * @method static Builder|DepartmentUser whereUpdatedAt($value)
 * @method static Builder|DepartmentUser whereUserId($value)
 * @mixin \Eloquent
 */
class DepartmentUser extends Model {
    
    protected $table = 'departments_users';
    
    protected $fillable = [
        'department_id', 'user_id', 'enabled'
    ];

    public function storeByDepartmentId($userId, array $departmentIds) {

        foreach ($departmentIds as $departmentId) {
            $this->create([
                'user_id' => $userId,
                'department_id' => $departmentId,
                'enabled' => 1,
            ]);
        }

    }
    
}
