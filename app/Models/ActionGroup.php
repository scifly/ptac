<?php
namespace App\Models;

use App\Helpers\Constant;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\ActionGroup
 *
 * @property int $id
 * @property int $action_id
 * @property int $group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ActionGroup whereActionId($value)
 * @method static Builder|ActionGroup whereCreatedAt($value)
 * @method static Builder|ActionGroup whereEnabled($value)
 * @method static Builder|ActionGroup whereGroupId($value)
 * @method static Builder|ActionGroup whereId($value)
 * @method static Builder|ActionGroup whereUpdatedAt($value)
 * @method static Builder|ActionGroup newModelQuery()
 * @method static Builder|ActionGroup newQuery()
 * @method static Builder|ActionGroup query()
 * @mixin Eloquent
 */
class ActionGroup extends Model {
    
    protected $table = 'actions_groups';
    
    protected $fillable = ['action_id', 'group_id', 'enabled'];
    
    /**
     * 根据groupId保存所有记录
     *
     * @param $groupId
     * @param array $ids
     * @return bool
     * @throws Throwable
     */
    function storeByGroupId($groupId, array $ids = []) {
        
        try {
            DB::transaction(function () use ($groupId, $ids) {
                $this->whereGroupId($groupId)->delete();
                foreach ($ids as $id) {
                    $records[] = array_combine(Constant::AG_FIELDS, [
                        $groupId, $id, now()->toDateTimeString(),
                        now()->toDateTimeString(), Constant::ENABLED,
                    ]);
                }
                $this->insert($records ?? []);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
