<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Request;

class EventController extends Controller {
    protected $event;

    function __construct(Event $event) {
        $this->event = $event;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*  public function index()
      {
          return view('event.index',[
              'js' => 'js/event/index.js',
              'fullcalendar' => true,
              ]);
      }*/

    /**
     * 根据用户id 显示个人的日历事件信息
     * @param $id = user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($userId) {
        //课程事件
//        $courseEvents = $this->event
//            ->where('iscourse', '1')
//            ->get()->toArray();
        //自定义事件
        $events = $this->event
            ->where('User_id', $userId)
            ->where('enabled', '0')
            ->get()->toArray();
        return view('event.index', [
            'js' => 'js/event/index.js',
            'fullcalendar' => true,
            'events' => $events
        ]);
    }

    public function create() {
        //

    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store() {
        $inputEvent = Request::all();
        if ($inputEvent['iscourse'] == 0) {
            $inputEvent['educator_id'] = '0';
            $inputEvent['subject_id'] = '0';
        }
        if ($inputEvent['iscourse'] == 0) {
            $inputEvent['alert_mins'] = '0';
        }
        $inputEvent['start'] = "1970-01-01 00:00:00";
        $inputEvent['end'] = "1970-01-01 00:00:00";
        $inputEvent['enabled'] = 0;
        if ($this->event->create($inputEvent)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event $event
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
        dd($this->event->find($id));
    }

    /**
     * Update the specified resource in storage.
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Event $event
     */
    public function update() {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if ($this->event->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 拖动列表添加日历事件
     */
    public function dragEvents() {
        $listJson = Request::all();
        //dd($listJson);
        $listEvent = $this->event->whereId($listJson['id'])->first(['title', 'remark', 'location', 'contact', 'url', 'start', 'end', 'ispublic', 'iscourse', 'educator_id', 'subject_id', 'alertable', 'alert_mins', 'user_id', 'enabled'])->toArray();
        $listEvent['start'] = $listJson['start'];
        $listEvent['end'] = $listJson['end'];
        $listEvent['enabled'] = 1;
        // dd($listJson['isRemoveList']);
        if ($listJson['isRemoveList'] == "true") {
            $this->destroy($listJson['id']);
        }
        //  dd( $listEvent['start']);
        //   dd( $listEvent);
        if ($this->event->create($listEvent)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        //  dd($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectEvents($userId) {

        return response()->json($this->event
            ->where('User_id', $userId)
            ->where('enabled', '1')
            ->get()->toArray());
    }

}
