<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\GroupMenu
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $group_id
 * @property int $menu_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $enabled
 * @method static Builder|GroupMenu whereCreatedAt($value)
 * @method static Builder|GroupMenu whereEnabled($value)
 * @method static Builder|GroupMenu whereGroupId($value)
 * @method static Builder|GroupMenu whereId($value)
 * @method static Builder|GroupMenu whereMenuId($value)
 * @method static Builder|GroupMenu whereUpdatedAt($value)
 */
class GroupMenu extends Model {
    
    protected $table = 'groups_menus';
    
    protected $fillable = ['group_id', 'menu_id', 'enabled'];
    
    public function storeByGroupId($groupId, array $ids = []) {
        try {
            $exception = DB::transaction(function () use ($groupId, $ids) {
                $this->where('group_id', $groupId)->delete();
                foreach ($ids as $id) {
                    $this->create([
                        'group_id' => $groupId,
                        'menu_id'  => $id,
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
