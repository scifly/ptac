<?php
namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Facades\{DB};
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
 * @method static Builder|DepartmentUser newModelQuery()
 * @method static Builder|DepartmentUser newQuery()
 * @method static Builder|DepartmentUser query()
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
     * @param null $custodian
     * @return bool
     * @throws Throwable
     */
    function storeByUserId($userId, array $departmentIds, $custodian = null) {
        
        try {
            DB::transaction(function () use ($userId, $departmentIds, $custodian) {
                $enabled = $custodian ? Constant::DISABLED : Constant::ENABLED;
                $this->where([
                    'user_id' => $userId,
                    'enabled' => $enabled
                ])->delete();
                $records = [];
                $record = ['user_id' => $userId, 'enabled' => $enabled];
                $departmentIds = array_unique($departmentIds);
                foreach ($departmentIds as $departmentId) {
                    $record['department_id'] = $departmentId;
                    $records[] = $record;
                }
                if ($schoolId = $this->schoolId()) {
                    $sDepartmentId = School::find($schoolId)->department_id;
                    $sGroupId = Group::whereName('学校')->first()->id;
                    if (
                        User::find($userId)->group_id == $sGroupId &&
                        !in_array($sDepartmentId, $departmentIds)
                    ) {
                        $record['department_id'] = $sDepartmentId;
                        $records[] = $record;
                    }
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
                foreach ($userIds as $userId) {
                    $du = $this->where([
                        'department_id' => $departmentId,
                        'user_id' => $userId
                    ])->first();
                    if (!$du) {
                        $records[] = [
                            'user_id'       => $userId,
                            'department_id' => $departmentId,
                            'created_at'    => now()->toDateTimeString(),
                            'updated_at'    => now()->toDateTimeString(),
                            'enabled'       => Constant::ENABLED,
                        ];
                    }
                }
                if (!empty($records)) {
                    $this->insert($records);
                    foreach (array_pluck($records, 'user_id') as $userId) {
                        $contacts[] = [$userId, '', 'update'];
                    }
                    (new User)->sync($contacts ?? []);
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
     * @param integer $userId
     * @param integer $departmentId
     * @return bool
     * @throws Throwable
     */
    function store($userId, $departmentId): bool {
        
        try {
            DB::transaction(function () use($userId, $departmentId) {
                $this->where('user_id', $userId)->delete();
                $this->create(
                    array_combine(
                        Constant::DU_FIELDS,
                        [$departmentId, $userId, 1]
                    )
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除部门 & 用户绑定关系
     *
     * @param $value
     * @return bool|null
     * @throws Throwable
     */
    function remove($value = null) {
        
        return $this->purge(
            [class_basename($this)],
            null, $value
        );
        
    }
    
}
