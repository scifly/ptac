<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessageCenterController extends Controller
{
    //
    
    /**
     * @return string
     */
    public function index(){
    
        return view('wechat.message_center.index');
    }
}
