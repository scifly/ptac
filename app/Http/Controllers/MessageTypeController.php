<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageTypeRequest;
use App\Models\MessageType;
use Illuminate\Support\Facades\Request;
class MessageTypeController extends Controller
{
    protected $messageType;

    function __construct(MessageType $messageType) {
        $this->messageType = $messageType;

    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->messageType->datatable());
        }
        return $this->output(__METHOD__);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->output(__METHOD__);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MessageTypeRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(MessageTypeRequest $request)
    {
        // request
        $data = $request->all();
        $row = $this->messageType->where(['name' => $data['name']])->first();

        if(!empty($row) ){

            return $this->fail('名称重复！');
        }else{

            return $this->messageType->create($data) ? $this->succeed() : $this->fail();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $messageType = $this->messageType->find($id);

        if (!$messageType) { return parent::notFound(); }
        return parent::output(__METHOD__, [
            'messageType' => $messageType,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $messageType = $this->messageType->find($id);

        if (!$messageType) { return parent::notFound(); }
        return parent::output(__METHOD__, [
            'messageType' => $messageType,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MessageTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function update(MessageTypeRequest $request, $id)
    {
        $data = MessageType::find($id);
        if (!$data) { return parent::notFound(); }
        $data->name = $request->input('name');
        $data->remark = $request->input('remark');
        $data->enabled = $request->input('enabled');
        $row = $this->messageType->where(['name' => $data->name])->first();
        if(!empty($row) && $row->id != $id){

            return $this->fail('名称重复！');
        }else{
            return $data->save() ? $this->succeed() : $this->fail();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $messageType = $this->messageType->find($id);

        if (!$messageType) { return parent::notFound(); }
        return $messageType->delete() ? parent::succeed() : parent::fail();
    }
}
