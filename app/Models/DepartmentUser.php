<?php

namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\DepartmentUser
 *
 * @property int $id
 * @property int $department_id 部门ID
 * @property int $user_id 用户ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|DepartmentUser whereCreatedAt($value)
 * @method static Builder|DepartmentUser whereDepartmentId($value)
 * @method static Builder|DepartmentUser whereEnabled($value)
 * @method static Builder|DepartmentUser whereId($value)
 * @method static Builder|DepartmentUser whereUpdatedAt($value)
 * @method static Builder|DepartmentUser whereUserId($value)
 * @mixin Eloquent
 */
class DepartmentUser extends Model {

    use ModelTrait;
    
    protected $table = 'departments_users';

    protected $fillable = ['department_id', 'user_id', 'enabled'];
    
    /**
     * 按UserId保存记录
     *
     * @param $userId
     * @param array $departmentIds
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function storeByUserId($userId, array $departmentIds) {
        
        try {
            DB::transaction(function () use ($userId, $departmentIds) {
                foreach ($departmentIds as $departmentId) {
                    self::create([
                        'user_id' => $userId,
                        'department_id' => $departmentId,
                        'enabled' => 1,
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 按部门Id保存记录
     *
     * @param $departmentId
     * @param array $userIds
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function storeByDepartmentId($departmentId, array $userIds) {
        
        try {
            DB::transaction(function () use ($departmentId, $userIds) {
                foreach ($userIds as $userId) {
                    self::create([
                        'user_id' => $userId,
                        'department_id' => $departmentId,
                        'enabled' => 1,
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }
    
}
