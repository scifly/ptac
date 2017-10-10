<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\messageSendingLogs
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @mixin \Eloquent
 */
class messageSendingLogs extends Model {
    
    protected $fillable = [
        'read_count',
        'received_count',
        'recipient_count',
    ];
    
    public function messages() {
        return $this->hasMany('App\Models\Message');
    }
    
    public function addMessageSendingLog($recipientCount) {
        try {
            $exception = DB::transaction(function () use ($recipientCount) {
                $log = $this->create([
                    'read_count'      => 0,
                    'received_count'  => 0,
                    'recipient_count' => $recipientCount,
                ]);
                return $log->id;
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $exception) {
            return false;
        }
        
    }
    
}
