<?php
namespace App\Models;

use App\Helpers\Constant;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SmsCharge - 短信充值记录
 *
 * @package App\Models
 * @property int $id
 * @property int $user_id 操作者用户id
 * @property int $target 充值对象: 1- 企业，2 - 学校，3 - 教职员工
 * @property int $targetid 充值对象id
 * @property int $amount 充值条数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|SmsCharge newModelQuery()
 * @method static Builder|SmsCharge newQuery()
 * @method static Builder|SmsCharge query()
 * @method static Builder|SmsCharge whereAmount($value)
 * @method static Builder|SmsCharge whereCreatedAt($value)
 * @method static Builder|SmsCharge whereId($value)
 * @method static Builder|SmsCharge whereTarget($value)
 * @method static Builder|SmsCharge whereTargetid($value)
 * @method static Builder|SmsCharge whereUpdatedAt($value)
 * @method static Builder|SmsCharge whereUserId($value)
 * @mixin Eloquent
 * @property-read User $user
 */
class SmsCharge extends Model {
    
    protected $table = 'sms_charges';
    
    protected $fillable = ['user_id', 'target', 'targetid', 'amount'];
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 保存充值记录
     *
     * @param $target - 充值对象
     * @param $targetid - 充值对象id
     * @param $amount - 充值条数
     * @return bool
     */
    function store($target, $targetid, $amount) {
        
        return $this->create(
            array_combine($this->fillable, [
                Auth::id(),
                Constant::SMS_CHARGE_TARGET[$target],
                $targetid, $amount
            ])
        ) ? true : false;
        
    }
    
    /**
     * 短信条数充值
     *
     * @param Model $model - 充值对象
     * @param $id
     * @param array $data
     * @return JsonResponse
     * @throws Throwable
     */
    function recharge(Model $model, $id, array $data) {
        
        try {
            DB::transaction(function () use ($model, $id, $data) {
                $target = lcfirst(class_basename($model));
                $record = $model->{'find'}($id);
                $target != 'user' ?: $record = $record->{'educator'};
                throw_if(
                    $target != 'corp' &&
                    ($data['charge'] > $record->{$target == 'school' ? 'corp' : 'school'}->{'sms_balance'}),
                    new Exception(__('messages.sms_charge.insufficient'))
                );
                $record->{'increment'}('sms_balance', $data['charge']);
                $this->store($target, $id, $data['charge']);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return response()->json([
            'title'   => __('messages.sms_charge.title'),
            'message' => __('messages.ok'),
            'quote'   => $model->{'find'}($id)->{'sms_balance'},
        ]);
        
    }

}
