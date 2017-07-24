<?php

namespace App\Http\Controllers;

use App\Services\Test;
use Illuminate\Support\Facades\DB;

class TestController extends Controller {
    protected $test;
    
    //
    
    function __construct(Test $test) {
        $this->test = $test;
    }
    
    function index() {


//        $sql='SELECT SQL_CALC_FOUND_ROWS Grade.id, Grade.name, School.name as schoolname, Grade.educator_ids, Grade.created_at, Grade.updated_at, Grade.enabled FROM grades AS Grade INNER JOIN schools AS School ON School.id = Grade.school_id ORDER BY Grade.id  DESC LIMIT 0, 10';
        $sqls= 'SELECT * from students';
        $data = DB::select($sqls);


    }
    
}
