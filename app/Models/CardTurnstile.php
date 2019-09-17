<?php
namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};
use Illuminate\Support\{Carbon, Facades\DB};
use Throwable;

/**
 * Class CardTurnstile
 *
 * @property int $id
 * @property int $card_id
 * @property int $turnstile_id
 * @property string|null $start_date 起始日期
 * @property string|null $end_date 截止日期
 * @property string $ruleids 通行规则ids
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
 * @method static Builder|CardTurnstile whereEndDate($value)
 * @method static Builder|CardTurnstile whereId($value)
 * @method static Builder|CardTurnstile whereRuleids($value)
 * @method static Builder|CardTurnstile whereStartDate($value)
 * @method static Builder|CardTurnstile whereTurnstileId($value)
 * @method static Builder|CardTurnstile whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CardTurnstile extends Pivot {
    
    protected $fillable = [
        'card_id', 'turnstile_id', 'start_date',
        'end_date', 'ruleids', 'enabled',
    ];
    
    /** @return BelongsTo */
    function card() { return $this->belongsTo('App\Models\Card'); }
    
    /** @return BelongsTo */
    function turnstile() { return $this->belongsTo('App\Models\Turnstile'); }
    
    /**
     * @param $cardIds
     * @param $turnstileIds
     * @param $start
     * @param $end
     * @param $ruleids
     * @return bool
     * @throws Throwable
     */
    function store($cardIds, $turnstileIds, $start, $end, $ruleids) {
        
        try {
            DB::transaction(function () use ($cardIds, $turnstileIds, $start, $end, $ruleids) {
                $timestamp = now()->toDateTimeString();
                foreach ($cardIds as $cardId) {
                    foreach ($turnstileIds as $turnstileId) {
                        $records[] = [
                            'card_id'      => $cardId,
                            'turnstile_id' => $turnstileId,
                            'start_date'   => $start,
                            'end_date'     => $end,
                            'ruleids'      => join(',', $ruleids[$turnstileId]),
                            'created_at'   => $timestamp,
                            'updated_at'   => $timestamp,
                            'enabled'      => 1,
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
