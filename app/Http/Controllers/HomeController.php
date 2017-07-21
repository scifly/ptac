<?php

namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Http\Requests\Request;
use App\Http\Requests\RegisterUser;
use App\Models\School;


class HomeController extends Controller {
    
    protected $school;
    
    /**
     * Create a new controller instance.
     *
     */
    public function __construct(School $school) {
        
        $this->school = $school;
        
        // $this->middleware('auth');

    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $data = $this->school->datatable();
        dd($data);

        // echo Wechat::getAccessToken('a', 'b', 'c');
        
        return view('home');
        
    }
}
