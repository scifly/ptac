<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\Test;
use Illuminate\Support\Facades\DB;

class TestController extends Controller {
    protected $test;
    
    //
    
    function __construct(Test $test) {
        $this->test = $test;
    }
    
    function index() {
        Company::destroy(2);
    }
    
}
