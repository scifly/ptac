<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

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
     * @throws Throwable
     */
    function storeByUserId($userId, array $departmentIds) {
        
        try {
            DB::transaction(function () use ($userId, $departmentIds) {
                $values = [];
                foreach ($departmentIds as $departmentId) {
                    $values[] = [
                        'user_id' => $userId,
                        'department_id' => $departmentId,
                        'enabled' => Constant::ENABLED,
                    ];
                }
                $this->insert($values);
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
                $values = [];
                foreach ($userIds as $userId) {
                    $values[] = [
                        'user_id' => $userId,
                        'department_id' => $departmentId,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'enabled' => Constant::ENABLED,
                    ];
                }
                $this->insert($values);
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
     * @param $user
     * @return void
     */
    function store(array $data, User $user): void {
        
        $du = [
            'user_id' => $user->id,
            'enabled' => Constant::ENABLED
        ];
        switch (Group::find($data['group_id'])->name) {
            case '运营':
                $du['department_id'] = Department::whereDepartmentTypeId(
                    DepartmentType::whereName('根')->first()->id
                )->first()->id;
                break;
            case '企业':
                $du['department_id'] = Corp::find($data['corp_id'])->department_id;
                break;
            case '学校':
                $du['department_id'] = School::find($data['school_id'])->department_id;
                break;
            case '学生':
                $du['department_id'] = Squad::find($data['class_id'])->department_id;
                break;
            case '监护人':
                $du['department_id'] = $data['department_id'];
                break;
            default:
                break;
        }
        
        $this->create($du);
        
    }
    
    
}
