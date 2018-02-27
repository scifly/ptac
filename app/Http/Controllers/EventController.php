<?php
namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 日历
 *
 * Class EventController
 * @package App\Http\Controllers
 */
class EventController extends Controller {
    
    protected $event;
    
    function __construct(Event $event) {
    
        $this->middleware(['auth']);
        $this->event = $event;
        
    }
    
    /**
     * 事件列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function index() {

        $user = Auth::user();
        $isAdmin = $this->event->getRole($user) ? 1 : 0;
        $events = $this->event
            ->where('User_id', $user->id)
            ->where('enabled', '0')
            ->get()->toArray();
        $show = true;
        return $this->output([
            'events'  => $events,
            'userId'  => $user->id,
            'isAdmin' => $isAdmin,
            'show' => $show,
        ]);
        
    }
    
    /**
     * 显示日历事件
     *
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function calendarEvents($userId) {
        return $this->event->showCalendar($userId);
        
    }
    
    /**
     * 新增一个列表事件
     *
     * @param EventRequest $request
     * @return Response
     * @internal param Request $request
     */
    public function store(EventRequest $request) {
        $inputEvent = $request->all();
        $listDate = $this->event->create($inputEvent);
        
        return $listDate ? $this->succeed($listDate) : $this->fail();
    }
    
    /**
     * 编辑日程事件的表单
     *
     * @param $id
     * @return Response
     * @internal param Event $event
     * @throws Throwable
     */
    public function edit($id) {
        //判断当前用户权限
        $row = Request::all();
        if ($row['ispublic'] == 1) {
            if (!$this->event->getRole($row['userId'])) {
                return $this->fail('此事件只有管理员可编辑！');
            }
        }
        $data = view('event.show', ['events' => $this->event->find($id)])->render();
        
        return !empty($data) ? $this->succeed($data) : $this->fail();
    }
    
    /**
     * 更新指定日历事件
     *
     * @param EventRequest $request
     * @param $id
     * @return Response
     * @internal param Request $request
     * @internal param Event $event
     */
    public function update(EventRequest $request, $id) {
        $input = $request->all();
        $input['enabled'] = 1;
        if ($input['end'] <= $input['start']) {
            return $this->fail('结束时间必须大于开始时间！');
        }
        //根据角色验证重复冲突
        if ($this->event->isValidateTime($input['user_id'], $input['educator_id'], $input['start'], $input['end'], $id)) {
            return $this->fail('时间有冲突！');
        }
        $event = $this->event->find($id);
        if (!$event) {
            return $this->notFound();
        }
        
        return $event->update($input) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除事件包括日历事件和列表事件
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Exception
     */
    public function destroy($id) {
        
        $event = $this->event->find($id);
        abort_if(!$event, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $event->delete()
        );
    }
    
    /**
     * 拖动列表添加日历事件
     */
    public function dragEvents() {
        $listJson = Request::all();
        $event = $this->event->whereId($listJson['id'])->first(['title', 'remark', 'location', 'contact', 'url', 'start', 'end', 'ispublic', 'iscourse', 'educator_id', 'subject_id', 'alertable', 'alert_mins', 'user_id', 'enabled'])->toArray();
        $event['start'] = $listJson['start'];
        $event['end'] = $listJson['end'];
        $event['enabled'] = 1;
        abort_if(
            $event['end'] <= $event['start'],
            HttpStatusCode::NOT_ACCEPTABLE,
            '结束时间必须大于开始时间！'
        );
        // 根据角色验证重复冲突
        
        if ($this->event->isValidateTime($event['user_id'], $event['educator_id'], $event['start'], $event['end'])) {
            abort(HttpStatusCode::NOT_ACCEPTABLE, '时间有冲突！');
        }
        
        return $this->result(
            $this->event->create($event)
        );
        
    }
    
    /**
     * 拖动实时保存日历事件
     *
     * @return JsonResponse
     */
    public function updateTime() {
        $data = Request::all();
        $event = $this->event->whereId($data['id'])->first();
        //计算多少秒
        $diffTime = $this->event->timeDiff($data['dayDiff'], $data['hoursDiff'], $data['minutesDiff']);
        //判断动作是否为拖动（拖动需同时改变start和end，缩放只需改变end）
        if (!isset($data['action'])) {
            $event->start = date("Y-m-d H:i:s", strtotime($event->start) + $diffTime);
        }
        $event->end = date("Y-m-d H:i:s", strtotime($event->end) + $diffTime);
        //根据角色验证重复冲突
        if ($this->event->isValidateTime($event->user_id, $event->educator_id, $event->start, $event->end, $event->id)) {
            abort(HttpStatusCode::NOT_ACCEPTABLE, '时间有冲突！');
        }
        
<<<<<<< HEAD
        return $event->save() ? $this->succeed() : $this->fail();
=======
        return $this->result(
            $event->save()
        );
        
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
    }
    
}
