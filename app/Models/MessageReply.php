<?php

namespace App\Models;

use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class MessageReply extends Model
{
    use ModelTrait;
    
    protected $fillable = [
        'msl_id','user_id','content'
    ];
    
    /**
     *
     */
    public function user() { return $this->belongsTo('App\Models\User','user_id','id'); }
    
    /**
     * @param $data
     * @return bool
     */
    static function store($data){
        $messageReply = self::create($data);
    
        return $messageReply ? true : false;
    }
    
    
}
