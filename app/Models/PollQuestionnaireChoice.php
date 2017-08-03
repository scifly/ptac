<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireChoice
 *
 * @mixin \Eloquent
 */
class PollQuestionnaireChoice extends Model {
    //
    protected $table = 'poll_questionnaire_subject_choices';

    protected $fillable = ['pqs_id','choice','seq_no','created_at','updated_at'];

    public function pollquestionnaireSubject()
    {
        return $this->belongsTo('App\Models\PollQuestionnaireSubject'
        ,'pqs_id'
        ,'id');
    }



}
