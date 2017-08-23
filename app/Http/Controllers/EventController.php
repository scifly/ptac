<?php

namespace App\Http\Controllers;

use App\Models\Educator;
use App\Models\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class EventController extends Controller {
    protected $event;

    function __construct(Event $event) {
        $this->event = $event;
    }

    /**
     * 根据用户id 显示列表 个人的日历事件信息
     * @param $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param $id = user_id
     */
    public function show($userId) {
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

    /**
     *显示日历事件
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarEvents($userId) {
        //判断userID的权限 1.管理员及以上  2.用户

        //通过userId找出educator_id
        $educator = Educator::where('user_id',$userId)->first();
        //先选出公开事件中 非课程的事件
        $pubNoCourseEvents = $this->event
            ->where('ispublic', 1)
            ->where('iscourse',0)
            ->get()->toArray();
        //选出公开事件中 课程事件
        $pubCourEvents = $this->event
            ->where('ispublic', 1)
            ->where('iscourse',1)
            ->where('educator_id',$educator->id)
            ->get()->toArray();

        //再选个人未公开事件
        $perEvents = $this->event
            ->where('User_id', $userId)
            ->where('ispublic', 0)
            ->where('enabled', '1')
            ->get()->toArray();
        //全部公共事件
        $pubEvents = $this->event->where('ispublic',1)->get()->toArray();
        //如果是管理员
        if($userId == 1){
            return  response()->json(array_merge($pubEvents, $perEvents));
        }
        //如果是用户
        return response()->json(array_merge($pubNoCourseEvents, $perEvents,$pubCourEvents));
    }

    /**
     * 新增一个列表事件
     *
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store() {
        $inputEvent = Request::all();
        if ($inputEvent['iscourse'] == 0) {
            $inputEvent['educator_id'] = '0';
            $inputEvent['subject_id'] = '0';
        }
        if ($inputEvent['alertable'] == 0) {
            $inputEvent['alert_mins'] = '0';
        }
        $inputEvent['start'] = "1970-01-01 00:00:00";
        $inputEvent['end'] = "1970-01-01 00:00:00";
        $inputEvent['enabled'] = 0;
        $listDate = $this->event->create($inputEvent);
        if ($listDate) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['listDate'] = $listDate;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 编辑日程事件的表单
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Event $event
     */
    public function edit($id) {
        //判断当前用户权限

        $data = view('event.show', ['events' => $this->event->findOrFail($id)])->render();
        if (!empty($data)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['data'] = $data;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }


    /**
     * 更新指定日历事件
     *
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Event $event
     */
    public function update($id) {
        $data = Request::all();
        if ($this->event->findOrFail($id)->update($data)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     *删除事件 包括日历事件和列表时间
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Event $event
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
        $listEvent = $this->event->whereId($listJson['id'])->first(['title', 'remark', 'location', 'contact', 'url', 'start', 'end', 'ispublic', 'iscourse', 'educator_id', 'subject_id', 'alertable', 'alert_mins', 'user_id', 'enabled'])->toArray();
        $listEvent['start'] = $listJson['start'];
        $listEvent['end'] = $listJson['end'];
        $listEvent['enabled'] = 1;

        if ($listJson['isRemoveList'] == "true") {
            $this->destroy($listJson['id']);
        }
        if ($this->event->create($listEvent)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 拖动实时保存
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTime() {
        $data = Request::all();
        $event = $this->event->whereId($data['id'])->first();
        //计算多少秒
        $days = $data['dayDiff'] * 24 * 60 * 60;
        $hours = $data['hoursDiff'] * 60 * 60;
        $minutes = $data['minutesDiff'] * 60;
        $diffTime = $days + $hours + $minutes;
        if (!isset($data['action'])) {
            $event->start = date("Y-m-d H:i:s", strtotime($event->start) + $diffTime);
        }
        $event->end = date("Y-m-d H:i:s", strtotime($event->end) + $diffTime);
        if ($event->save()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    /**
     * 判断当前用户权限
     */
//    private function getPermissions(){
//
//        $uId = Session::get('user');
//    }
}
