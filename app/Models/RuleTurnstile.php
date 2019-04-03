<?php
namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * 通行规则与门禁绑定关系
 * 
 * Class RuleTurnstile
 *
 * @package App\Models
 * @property int $id
 * @property int $turnstile_id 门禁id
 * @property int $door 门编号：1 - 4
 * @property int $passage_rule_id 通行规则id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Turnstile $turnstile
 * @property-read PassageRule $passageRule
 * @method static Builder|RuleTurnstile newModelQuery()
 * @method static Builder|RuleTurnstile newQuery()
 * @method static Builder|RuleTurnstile query()
 * @method static Builder|RuleTurnstile whereCreatedAt($value)
 * @method static Builder|RuleTurnstile whereEnabled($value)
 * @method static Builder|RuleTurnstile whereId($value)
 * @method static Builder|RuleTurnstile wherePassageRuleId($value)
 * @method static Builder|RuleTurnstile whereTurnstileId($value)
 * @method static Builder|RuleTurnstile whereUpdatedAt($value)
 * @method static Builder|RuleTurnstile whereDoor($value)
 * @mixin Eloquent
 */
class RuleTurnstile extends Model {
    
    protected $table = 'rules_turnstiles';
    
    protected $fillable = [
        'turnstile_id', 'door', 'passage_rule_id', 'enabled'
    ];
    
    /**
     * 返回指定绑定关系对应的门禁对象
     *
     * @return BelongsTo
     */
    function turnstile() { return $this->belongsTo('App\Models\Turnstile'); }
    
    /**
     * 返回指定绑定关系对应的通行规则对象
     *
     * @return BelongsTo
     */
    function passageRule() { return $this->belongsTo('App\Models\PassageRule'); }
    
    /**
     * @param $passageRuleId
     * @param $ids
     * @return bool
     * @throws Throwable
     */
    function store($passageRuleId, $ids) {
        
        try {
            DB::transaction(function () use ($passageRuleId, $ids) {
                $doors = (new Turnstile)->doors();
                $this->wherePassageRuleId($passageRuleId)->delete();
                $timestamp = now()->toDateTimeString();
                $record = [
                    'passage_rule_id' => $passageRuleId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'enabled' => 1
                ];
                foreach ($ids as $id) {
                    list($sn, $door) = explode('.', $doors[$id]);
                    $t = Turnstile::whereSn($sn)->first();
                    $records[] = array_merge(
                        $record, ['turnstile_id' => $t->id, 'door' => $door]
                    );
                }
                $this->insert($records ?? []);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}