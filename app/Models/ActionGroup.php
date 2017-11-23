<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\ActionGroup
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $action_id
 * @property int $group_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ActionGroup whereActionId($value)
 * @method static Builder|ActionGroup whereCreatedAt($value)
 * @method static Builder|ActionGroup whereEnabled($value)
 * @method static Builder|ActionGroup whereGroupId($value)
 * @method static Builder|ActionGroup whereId($value)
 * @method static Builder|ActionGroup whereUpdatedAt($value)
 */
class ActionGroup extends Model {

    protected $table = 'actions_groups';

    protected $fillable = ['action_id', 'group_id', 'enabled'];

    public function storeByGroupId($groupId, array $ids = []) {

        try {
            $exception = DB::transaction(function () use ($groupId, $ids) {
                $this->where('group_id', $groupId)->delete();
                foreach ($ids as $id) {
                    $this->create([
                        'group_id'  => $groupId,
                        'action_id' => $id,
                        'enabled'   => 1,
                    ]);
                }
            });

            return !is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

}
