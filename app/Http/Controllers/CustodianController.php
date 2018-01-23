<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Department;
use App\Models\School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * 监护人
 *
 * Class CustodianController
 * @package App\Http\Controllers
 */
class CustodianController extends Controller {


    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);

    }
    
    /**
     * 监护人列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                Custodian::datatable()
            );
        }

        return $this->output();
        
    }
    
    /**
     * 创建监护人
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {

        if (Request::method() === 'POST') {
            $field = Request::query('field');
            $id = Request::query('id');
            $this->result['html'] = School::getFieldList($field, $id);
            return response()->json($this->result);
        }

        return $this->output();

    }
    
    /**
     * 保存监护人
     *
     * @param CustodianRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(CustodianRequest $request) {

        return $this->result(Custodian::store($request));

    }
    
    /**
     * 监护人详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id){
        
        $custodian = Custodian::find($id);

        return $this->output(['custodian'  => $custodian]);
        
    }
    
    /**
     * 编辑监护人
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {

        if (Request::method() === 'POST') {
            $field = Request::query('field');
            $id = Request::query('id');
            if($field && $id) {
                $this->result['html'] = School::getFieldList($field, $id);
                return response()->json($this->result);
            } else {
                return response()->json(Department::tree());
            }
        }
        $custodian = Custodian::find($id);
        $pupils = CustodianStudent::whereCustodianId($id)->get();
        return $this->output([
            'mobiles'   => $custodian->user->mobiles,
            'custodian' => $custodian,
            'pupils'    => $pupils,
        ]);

    }
    
    /**
     * 更新监护人.
     * @param CustodianRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(CustodianRequest $request, $id) {

        return $this->result(Custodian::modify($request, $id));

    }
    
    /**
     * 删除指定的监护人
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(Custodian::remove($id));
        
    }
    
    /**
     * 导出监护人
     *
     * @return void
     */
    public function export() {
        
        $data = Custodian::export();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        Excel::create(iconv('UTF-8', 'GBK', '监护人列表'), function ($excel) use ($data) {
            /** @noinspection PhpUndefinedMethodInspection */
            $excel->sheet('score', function($sheet) use ($data) {
                /** @noinspection PhpUndefinedMethodInspection */
                $sheet->rows($data);
                /** @noinspection PhpUndefinedMethodInspection */
                $sheet->setWidth(array(
                    'A'     =>  30,
                    'B'     =>  30,
                    'C'     =>  30,
                    'D'     =>  30,
                    'E'     =>  30,
                    'F'     =>  30,
                ));
                
            });
        },'UTF-8')->export('xls');
        
    }
    
}
