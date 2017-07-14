<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Media
 *
 * @mixin \Eloquent
 */
class Media extends Model {
    //
    protected $table='medias';

    protected $fillable=['path','remark','media_type_id','created_at','updated_at','enabled'];
}
