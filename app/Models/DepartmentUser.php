<?php

namespace App\Models;

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
    
    /**
     * 返回指定部门(含子部门）下的所有用户id
     *
     * @param $departmentId
     * @return array
     */
    function userIds($departmentId): array {

        $departmentIds[] = $departmentId;
        $department = new Department();
        $departmentIds = array_unique(
            array_merge(
                $department->subDepartmentIds($departmentId), $departmentIds
            )
        );
        $userIds = [];
        foreach ($departmentIds as $id) {
            $userIds = array_merge(
                self::whereDepartmentId($id)->pluck('user_id')->toArray(),
                $userIds
            );
        }
        
        return array_unique($userIds);
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有学生Id
     *
     * @param $departmentId
     * @return array
     */
    function studentIds($departmentId): array {
    
        return $this->getIds($departmentId, 'student');
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有监护人Id
     *
     * @param $departmentId
     * @return array
     */
    function custodianIds($departmentId): array {
        
        return $this->getIds($departmentId, 'custodian');
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有教职员工Id
     *
     * @param $departmentId
     * @return array
     */
    function educatorIds($departmentId): array {
        
        return $this->getIds($departmentId, 'educator');
        
    }
    
    /**
     * 返回对指定用户可见的所有部门Id
     *
     * @param $userId
     * @return array
     */
    function departmentIds($userId) {
        
        $departmentIds = [];
        $user = User::find($userId);
        foreach ($user->departments as $d) {
            $departmentIds[] = $d->id;
            $departmentIds = array_merge(
                $d->subDepartmentIds($d->id),
                $departmentIds
            );
        }
        
        return array_unique($departmentIds);
        
    }
    
    /**
     * @param $departmentId
     * @param $type
     * @return array
     */
    private function getIds($departmentId, $type): array {
        
        $ids = [];
        $userIds = $this->userIds($departmentId);
        foreach ($userIds as $id) {
            $$type = User::find($id)->{$type};
            if ($$type) {
                $ids[] = $$type->id;
            }
        }
        
        return $ids;
        
    }
    
}
