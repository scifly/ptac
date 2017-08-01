<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';
    protected $fillable = [
        'grade_id',
        'name',
        'educator_ids'
    ];

    /**
     * 年级与班级
     */
    public function grades()
    {
        return $this->belongsTo('App\Models\Grade');
    }
}
