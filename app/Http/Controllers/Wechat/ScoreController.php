<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;

class ScoreController extends Controller{

    public function index()
    {

        return view('wechat.scores.students_score_lists');
    }
}