<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Carbon;

/**
 * 通行规则与门禁绑定关系
 * 
 * Class RuleTurnstile
 *
 * @package App\Models
 * @property int $id
 * @property int $turnstile_id 门禁id
 * @property int $passage_rule_id 通行规则id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|RuleTurnstile newModelQuery()
 * @method static Builder|RuleTurnstile newQuery()
 * @method static Builder|RuleTurnstile query()
 * @method static Builder|RuleTurnstile whereCreatedAt($value)
 * @method static Builder|RuleTurnstile whereEnabled($value)
 * @method static Builder|RuleTurnstile whereId($value)
 * @method static Builder|RuleTurnstile wherePassageRuleId($value)
 * @method static Builder|RuleTurnstile whereTurnstileId($value)
 * @method static Builder|RuleTurnstile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RuleTurnstile extends Model {
    
    protected $table = 'rules_turnstiles';
    
    protected $fillable = [
        'turnstile_id', 'passage_rule_id', 'enabled'
    ];
    
}
