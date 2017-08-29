<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorClassRequest;

use App\Models\Educator;
use App\Models\EducatorClass;

use Illuminate\Support\Facades\Request;

class EducatorClassController extends Controller
{
    protected $educatorClass;

    protected $educator;


    /**
     * SubjectModulesController constructor.
     * @param EducatorClass $educatorClass
     * @param Educator $educator
     */
    function __construct(EducatorClass $educatorClass, Educator $educator)
    {
        $this->educatorClass = $educatorClass;
        $this->educator = $educator;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->educatorClass->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return parent::output(__METHOD__);
    }

    /**
     *添加.
     *
     * @param EducatorClassRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EducatorClassRequest $request)
    {
        $data = $request->all();
        if ($this->educator->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->educatorClass->create($data) ? $this->succeed() : $this->fail();

    }

    /**
     * Display the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param EducatorClass $educatorClass
     */
    public function show($id)
    {
        return view('educator_class.show', ['educatorClass' => $this->educatorClass->findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param EducatorClass $educatorClass
     */
    public function edit($id)
    {
        $educatorClass = $this->educatorClass->find($id);
        if (!$educatorClass) { return $this->notFound(); }
        return $this->output(__METHOD__, ['educatorClass' => $educatorClass]);

    }

    /**
     * @param EducatorClassRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EducatorClassRequest $request, $id)
    {
        $educatorClass = $this->educatorClass->find($id);
        if (!$educatorClass) { return $this->notFound(); }
        if ($this->educatorClass->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $educatorClass->update($request->all()) ? $this->succeed() : $this->fail();

    }

    /**
     * 删除教职员工
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param EducatorClass $educatorClass
     */
    public function destroy($id)
    {
        $educatorClass = $this->educatorClass->find($id);
        if (!$educatorClass) { return $this->notFound(); }
        return $educatorClass->delete() ? $this->succeed() : $this->fail();
    }
}
