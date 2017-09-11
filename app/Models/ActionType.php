<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActionType
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $name action类型名称
 * @property string|null $remark 备注
 * @property int $enabled
 * @method static Builder|School whereCreatedAt($value)
 * @method static Builder|School whereEnabled($value)
 * @method static Builder|School whereId($value)
 * @method static Builder|School whereName($value)
 * @method static Builder|School whereRemark($value)
 * @method static Builder|School whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ActionType extends Model {
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
}

