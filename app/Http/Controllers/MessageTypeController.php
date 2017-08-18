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
        return view('message_type.index' ,
            [
                'js' => 'js/message_type/index.js',
                'dialog' => true,
                'datatable' => true,
            ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('message_type.create',[
            'js' => 'js/message_type/create.js',
            'form' => true
        ]);
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
        if(!empty($row)){
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '名称重复！';
        }else{
            if($this->messageType->create($data))
            {
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';
            }
        }

        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $messageType = MessageType::whereId($id)->first();

        return view('message_type.show', [
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
        $messageType = $this->messageType->whereId($id)->first();

        return view('message_type.edit', [
            'js' => 'js/message_type/edit.js',
            'messageType' => $messageType,
            'form' => true

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

        $data->name = $request->input('name');
        $data->remark = $request->input('remark');
        $data->enabled = $request->input('enabled');
        $row = $this->messageType->where(['name' => $data->name])->first();
        if(!empty($row) && $row->id != $id){

            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '名称重复！';

        }else{
            if($data->save())
            {
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';

            }
        }

        return response()->json($this->result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->messageType->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}
