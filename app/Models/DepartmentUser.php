<?php
namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\DepartmentUser 部门 & 用户绑定关系
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
     * @throws Throwable
     */
    function storeByUserId($userId, array $departmentIds) {
        
        try {
            DB::transaction(function () use ($userId, $departmentIds) {
                $records = [];
                foreach ($departmentIds as $departmentId) {
                    $records[] = [
                        'user_id'       => $userId,
                        'department_id' => $departmentId,
                        'enabled'       => Constant::ENABLED,
                    ];
                }
                $this->insert($records);
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
     * @throws Throwable
     */
    function storeByDepartmentId($departmentId, array $userIds) {
        
        try {
            DB::transaction(function () use ($departmentId, $userIds) {
                $records = [];
                $userids = [];
                foreach ($userIds as $userId) {
                    $du = $this::whereDepartmentId($departmentId)
                        ->where('user_id', $userId)->first();
                    if (!$du) {
                        $records[] = [
                            'user_id'       => $userId,
                            'department_id' => $departmentId,
                            'created_at'    => now()->toDateTimeString(),
                            'updated_at'    => now()->toDateTimeString(),
                            'enabled'       => Constant::ENABLED,
                        ];
                        $userids[] = $userId;
                    }
                }
                if (!empty($records)) {
                    $this->insert($records);
                    Auth::user()->batchUpdateWechatUsers($userids);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 保存用户部门数据
     *
     * @param array $data
     * @return bool
     */
    function store(array $data): bool {
        
        return $this->create($data) ? true : false;
        
    }
    
}
