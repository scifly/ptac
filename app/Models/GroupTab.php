<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\GroupTab
 *
 * @mixin \Eloquent
 */
class GroupTab extends Model {
    
    protected $table = 'groups_tabs';
    
    protected $fillable = ['group_id', 'tab_id', 'enabled'];
    
    public function storeByGroupId($groupId, array $ids = []) {
        
        try {
            $exception = DB::transaction(function() use ($groupId, $ids) {
                $this->where('group_id', $groupId)->delete();
                foreach ($ids as $id) {
                    $this->create([
                        'group_id' => $groupId,
                        'tab_id' => $id,
                        'enabled' => 1
                    ]);
                }
            });
            return !is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
}
