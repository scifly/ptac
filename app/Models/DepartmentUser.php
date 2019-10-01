<?php
namespace App\Models;

use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};
use Illuminate\Support\{Arr, Collection, Facades\DB};
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
 * @property-read Department $dept
 * @property-read User $user
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
class DepartmentUser extends Pivot {
    
    use ModelTrait;
    
    protected $fillable = ['department_id', 'user_id', 'enabled'];
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function dept() { return $this->belongsTo('App\Models\Department', 'department_id'); }
    
    /**
     * 按UserId保存记录
     *
     * @param $userId
     * @param array $deptIds
     * @param null $custodian
     * @return bool
     * @throws Throwable
     */
    function storeByUserId($userId, array $deptIds, $custodian = null) {
        
        try {
            DB::transaction(function () use ($userId, $deptIds, $custodian) {
                $enabled = $custodian ? Constant::DISABLED : Constant::ENABLED;
                $condition = $record = array_combine(
                    ['user_id', 'enabled'], [$userId, $enabled]
                );
                $this->where($condition)->delete();
                foreach (($deptIds = array_unique($deptIds)) as $deptId) {
                    $record['department_id'] = $deptId;
                    $records[] = $record;
                }
                if ($schoolId = $this->schoolId()) {
                    $sDeptId = School::find($schoolId)->department_id;
                    $sGroupId = Group::whereName('学校')->first()->id;
                    if (
                        User::find($userId)->group_id == $sGroupId &&
                        !in_array($sDeptId, $deptIds)
                    ) {
                        $record['department_id'] = $sDeptId;
                        $records[] = $record;
                    }
                }
                $this->insert($records ?? []);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 按部门Id保存记录
     *
     * @param $deptId
     * @param Collection $userIds
     * @return bool
     * @throws Throwable
     */
    function storeByDeptId($deptId, Collection $userIds) {
        
        try {
            DB::transaction(function () use ($deptId, $userIds) {
                foreach ($userIds as $userId) {
                    $where = ['department_id' => $deptId, 'user_id' => $userId];
                    if (!$this->where($where)->first()) {
                        $records[] = [
                            'user_id'       => $userId,
                            'department_id' => $deptId,
                            'created_at'    => now()->toDateTimeString(),
                            'updated_at'    => now()->toDateTimeString(),
                            'enabled'       => Constant::ENABLED,
                        ];
                    }
                }
                if (!empty($records)) {
                    $this->insert($records);
                    foreach (Arr::pluck($records, 'user_id') as $userId) {
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
     * @param integer $deptId
     * @return bool
     * @throws Throwable
     */
    function store($userId, $deptId): bool {
        
        try {
            DB::transaction(function () use ($userId, $deptId) {
                $this->where('user_id', $userId)->delete();
                $this->create(array_combine($this->fillable, [$deptId, $userId, 1]));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除部门 & 用户绑定关系
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
}
