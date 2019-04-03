<?php
namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * 一卡通 & 门禁绑定关系
 * 
 * Class CardTurnstile
 *
 * @package App\Models
 * @property int $id
 * @property int $card_id
 * @property int $turnstile_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Card $card
 * @property-read Turnstile $turnstile
 * @method static Builder|CardTurnstile newModelQuery()
 * @method static Builder|CardTurnstile newQuery()
 * @method static Builder|CardTurnstile query()
 * @method static Builder|CardTurnstile whereCardId($value)
 * @method static Builder|CardTurnstile whereCreatedAt($value)
 * @method static Builder|CardTurnstile whereEnabled($value)
 * @method static Builder|CardTurnstile whereId($value)
 * @method static Builder|CardTurnstile whereTurnstileId($value)
 * @method static Builder|CardTurnstile whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CardTurnstile extends Model {

    protected $table = 'cards_turnstiles';
    
    protected $fillable = [
        'card_id', 'turnstile_id', 'start_date',
        'end_date', 'pr_ids', 'enabled'
    ];
    
    /**
     * 返回指定绑定关系对应的一卡通对象
     *
     * @return BelongsTo
     */
    function card() { return $this->belongsTo('App\Models\Card'); }
    
    /**
     * 返回指定绑定关系对应的门禁对象
     *
     * @return BelongsTo
     */
    function turnstile() { return $this->belongsTo('App\Models\Turnstile'); }
    
    /**
     * @param $cardIds
     * @param $turnstileIds
     * @return bool
     * @throws Throwable
     */
    function store($cardIds, $turnstileIds) {
        
        try {
            DB::transaction(function () use ($cardIds, $turnstileIds) {
                $this->whereIn('card_id', $cardIds)->delete();
                foreach ($cardIds as $cardId) {
                    foreach ($turnstileIds as $turnstileId) {
                        $records[] = [
                            'card_id' => $cardId,
                            'turnstile_id' => $turnstileId,
                            'created_at' => now()->toDateTimeString(),
                            'updated_at' => now()->toDateTimeString(),
                            'enabled' => 1
                        ];
                    }
                }
                $this->insert($records ?? []);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
