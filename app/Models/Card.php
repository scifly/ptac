<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 一卡通
 * 
 * Class Card
 *
 * @package App\Models
 * @property int $id
 * @property string $sn 卡号
 * @property int $user_id 所属用户id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status 状态
 * @property-read User $user
 * @method static Builder|Card newModelQuery()
 * @method static Builder|Card newQuery()
 * @method static Builder|Card query()
 * @method static Builder|Card whereCreatedAt($value)
 * @method static Builder|Card whereId($value)
 * @method static Builder|Card whereStatus($value)
 * @method static Builder|Card whereUpdatedAt($value)
 * @method static Builder|Card whereUserId($value)
 * @method static Builder|Card whereSn($value)
 * @mixin Eloquent
 */
class Card extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['sn', 'user_id', 'status'];
    
    /**
     * 获取一卡通对应的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 保存一卡通
     *
     * @param User|null $user
     * @return bool
     * @throws Throwable
     */
    function store(User $user = null) {
        
        try {
            DB::transaction(function () use ($user) {
                $data = Request::all();
                if ($user) {
                    if (Request::method() == 'POST') {
                        if (!empty($data['card']['sn'])) {
                            $card = Card::create([
                                'sn' => $data['card_sn'],
                                'user_id' => $user->id,
                                'status' => 1
                            ]);
                            $user->update(['card_id' => $card->id]);
                        }
                    } else {
                        if (!empty($data['card']['sn'])) {
                            if ($user->card) {
                                $user->card->update($data['card']);
                            } else {
                                $card = Card::create(
                                    array_merge($data['card'], [
                                        'user_id' => $user->id,
                                        'status' => 1
                                    ])
                                );
                                $user->update(['card_id' => $card->id]);
                            }
                        } else {
                            $user->card->delete();
                        }
                    }
                } else {
                    $this->create($data);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }

}
