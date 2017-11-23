<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\GroupTab
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $group_id
 * @property int $tab_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|GroupTab whereCreatedAt($value)
 * @method static Builder|GroupTab whereEnabled($value)
 * @method static Builder|GroupTab whereGroupId($value)
 * @method static Builder|GroupTab whereId($value)
 * @method static Builder|GroupTab whereTabId($value)
 * @method static Builder|GroupTab whereUpdatedAt($value)
 */
class GroupTab extends Model {
    
    protected $table = 'groups_tabs';
    
    protected $fillable = ['group_id', 'tab_id', 'enabled'];
    
    public function storeByGroupId($groupId, array $ids = []) {
        
        try {
            $exception = DB::transaction(function () use ($groupId, $ids) {
                $this->where('group_id', $groupId)->delete();
                foreach ($ids as $id) {
                    $this->create([
                        'group_id' => $groupId,
                        'tab_id'   => $id,
                        'enabled'  => 1,
                    ]);
                }
            });
            
            return !is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
}
