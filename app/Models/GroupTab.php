<?php
namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupTab whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupTab whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupTab whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupTab whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupTab whereTabId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupTab whereUpdatedAt($value)
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
