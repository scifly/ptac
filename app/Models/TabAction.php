<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TabAction
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $tab_id 卡片ID
 * @property int $action_id 控制器action ID
 * @property int $default 是否为默认加载的控制器action
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TabAction whereActionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TabAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TabAction whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TabAction whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TabAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TabAction whereTabId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TabAction whereUpdatedAt($value)
 */
class TabAction extends Model {
    
    protected $table = 'tabs_actions';
    protected $fillable = [
        'tab_id',
        'action_id',
        'default',
        'enabled'
    ];
    
}
