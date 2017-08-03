<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireChoice
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $pqs_id 题目ID
 * @property string $choice 选项内容
 * @property int $seq_no 选项排序编号
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\PollQuestionnaireSubject $pollquestionnaireSubject
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireChoice whereChoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireChoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireChoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireChoice wherePqsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireChoice whereSeqNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireChoice whereUpdatedAt($value)
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
