<?php
namespace App\Services;

class Test {
    
    protected $data;
    
    function __construct($data) {
        $this->data = $data;
    }
    
    public function respond($headers = []) {
        return response()->json($this->data, '200', $headers);
    }
    
}