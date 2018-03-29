<?php
namespace App\Http\Controllers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Grade;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Request;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller {
    
    use ModelTrait;
    
    const ALLOWED_CORP_ACTIONS = [
        '/corps/edit/%s',
        '/corps/update/%s',
    ];
    const ALLOWED_SCHOOL_ACTIONS = [
        '/schools/show/%s',
        '/schools/edit/%s',
        '/schools/update/%s',
    ];
    const ALLOWED_WAPSITE_ACTIONS = [
        '/wap_sites/show/%s',
        '/wap_sites/edit/%s',
        '/wap_sites/update/%s',
    ];
    
    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function index() {

        dd(Request::url());
        
        dd(implode(',', [1,2,3]));
        $start = '2018-03-01';
        $end = '2018-03-07';
        $d1 = Carbon::createFromTimestamp(strtotime($start));
        $d2 = Carbon::createFromTimestamp(strtotime($end));
        dd($d1->addDay()->toDateString());
        dd($d2->diffInDays($d1));
    
        // $students = Student::with('user:id,realname')->get()->pluck('user.realname', 'id');
        $grade = new Grade();
        list($classes) = $grade->classList(1);
        dd($classes);
        $inputFileName = $filePath =
            'uploads/'
            . date('Y')
            . '/'
            . date('m')
            . '/'
            . date('d')
            . '/'
            . '1718165aab8bd87e514.xlsx';
        $spreadsheet = IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        var_dump($sheetData);exit;
        try {
            $client = new Client();
            $reponse = $client->post(
                'http://sandbox.ddd/ptac/public/api/login', [
                    'form_params' => [
                        'username' => 'haoyuhang',
                        'password' => '#ilikeit09'
                    ]
                ]
            );
            $token = json_decode($reponse->getBody()->getContents())->{'token'};
            $response = $client->post(
                'http://sandbox.ddd/ptac/public/api/upload_consumption', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'form_params' => [
                        'student_id' => 4,
                        'location' => '食堂',
                        'machineid' => 'm123456',
                        'ctype' => 0,
                        'amount' => 25.50,
                        'ctime' => '2018-03-15 14:25:30',
                        'merchant' => '青椒肉丝套饭'
                    ]
                ]
            );
            dd(json_decode($reponse->getBody(), true));
        } catch (ClientException $e ) {
            echo $e->getResponse()->getStatusCode();
            echo $e->getResponse()->getBody()->getContents();
        }
    
    }
    
    public function listen() {
        
        return view('test.listen');
        
    }
    
    function getTraitMethodsRefs(ReflectionClass $class) {
        
        $traitMethods = call_user_func_array(
            'array_merge',
            array_map(
                function (ReflectionClass $ref) { return $ref->getMethods(); },
                $class->getTraits()
            )
        );
        $traitMethods = call_user_func_array(
            'array_merge',
            array_map(
                function (ReflectionMethod $method) { return [spl_object_hash($method) => $method->getName()]; },
                $traitMethods
            )
        );
        
        return $traitMethods;
        
    }
    
    function getClassMethodsRefs(ReflectionClass $class) {
        
        return call_user_func_array(
            'array_merge',
            array_map(
                function (ReflectionMethod $method) { return [spl_object_hash($method) => $method->getName()]; },
                $class->getMethods()
            )
        );
        
    }
    
    private function getLevel($id, &$level) {
        
        /** @var Department $parent */
        $parent = Department::find($id)->parent;
        if ($parent) {
            $level += 1;
            $this->getLevel($parent->id, $level);
        }
        return $level;
        
    }
    
}
