<?php
namespace App\Http\Controllers;

use App\Http\Requests\EducatorRequest;
use App\Models\Department;
use App\Models\Educator;
use App\Models\EducatorClass;
use App\Models\Mobile;
use App\Models\Team;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 教职员工
 *
 * Class EducatorController
 * @package App\Http\Controllers
 */
class EducatorController extends Controller {
    
    protected $educator;
    protected $mobile;
    protected $educatorClass;
    protected $team;
    protected $department;
    
    public function __construct(
        Educator $educator,
        Mobile $mobile,
        EducatorClass $educatorClass,
        Team $team,
        Department $department
    ) {
    
        $this->middleware(['auth']);
        $this->educator = $educator;
        $this->mobile = $mobile;
        $this->educatorClass = $educatorClass;
        $this->team = $team;
        $this->department = $department;
        
    }
    
    /**
     * 教职员工列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->educator->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建教职员工
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        if (Request::method() === 'POST') {
            return $this->department->tree();
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存教职员工
     *
     * @param EducatorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EducatorRequest $request) {
        
        return $this->educator->store($request)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 教职员工详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'educator'  => $educator,
            'educators' => $this->educator->teams(),
        ]);
        
    }
    
    /**
     * 编辑教职员工
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        if (Request::method() === 'POST') {
            return $this->department->tree();
        }
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        $selectedTeams = [];
        foreach ($educator->teams as $v) {
            $selectedTeams[$v->id] = $v->name;
        }
        $selectedDepartmentIds = [];
        foreach ($educator->user->departments as $department) {
            $selectedDepartmentIds[] = $department->id;
        }
        $selectedDepartments = $this->department->selectedNodes($selectedDepartmentIds);
        return $this->output(__METHOD__, [
            'mobiles'               => $educator->user->mobiles,
            'educator'              => $educator,
            'selectedTeams'         => $selectedTeams,
            'selectedDepartmentIds' => implode(',', $selectedDepartmentIds),
            'selectedDepartments'   => $selectedDepartments,
        ]);
        
    }
    
    /**
     * 教职员工充值
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function recharge($id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, [
            'educator' => $educator,
        ]);
        
    }
    
    /**
     * 更新教职员工
     *
     * @param EducatorRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EducatorRequest $request, $id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) {
            return $this->notFound();
        }
        return $educator->modify($request) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 更新教职员工充值
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechargeStore($id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) {
            return $this->notFound();
        }
        $recharge = Request::get('recharge');
        $educator->sms_quote += $recharge;
        return $educator->save() ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除教职员工
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) {
            return $this->notFound();
        }
        return $this->educator->remove($id, true)
            ? parent::succeed() : parent::fail();
        
    }
    /**
     * 导入教职员工
     */
    public function import() {
        
        if (Request::isMethod('post')) {
            $file = Request::file('file');
            if (empty($file)) {
                $result = [
                    'statusCode' => 500,
                    'message' => '您还没选择文件！',
                ];
                return response()->json($result);
            }
            // 文件是否上传成功
            if ($file->isValid()) {
                $this->educator->upload($file);
            }
        }
    }
    
    /**
     * 导出教职员工
     * @return \Illuminate\Http\JsonResponse
     */
    public function export() {
        $id = Request::query('id');
        if ($id) {
            $data = $this->educator->export($id);
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            Excel::create(iconv('UTF-8', 'GBK', '教职员工列表'), function ($excel) use ($data) {
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
}
