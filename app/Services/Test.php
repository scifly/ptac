<?php
namespace App\Services;
use Illuminate\Http\JsonResponse;

/**
 * Class Test
 * @package App\Services
 */
class Test {

    protected $data;
    
    /**
     * Test constructor.
     * @param $data
     */
    function __construct($data) {
        $this->data = $data;
    }
    
    /**
     * @param array $headers
     * @return JsonResponse
     */
    public function respond($headers = []) {
        return response()->json($this->data, '200', $headers);
    }

}