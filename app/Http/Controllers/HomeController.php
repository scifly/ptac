<?php

namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Http\Requests\Request;


class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     */
    public function __construct() {

        $this->middleware('auth');

    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        echo Wechat::getAccessToken('a', 'b', 'c');
        
        return view('home');
        
    }
}
