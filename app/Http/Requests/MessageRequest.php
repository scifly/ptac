<?php
namespace App\Http\Requests;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\CommType;
use App\Models\MessageType;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class MessageRequest
 * @package App\Http\Requests
 */
class MessageRequest extends FormRequest {
    
    use ModelTrait;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        $rules = [
            'comm_type_id'    => 'required|integer',
            'app_id'          => 'required|integer',
            'msl_id'          => 'required|integer',
            'title'           => 'required|string|max:64',
            'serviceid'       => 'required|string|max:255',
            'message_id'      => 'required|integer',
            'url'             => 'required|string|max:255',
            'media_ids'       => 'required|string|max:255',
            's_user_id'       => 'required|integer',
            'r_user_id'       => 'required|integer',
            'message_type_id' => 'required|integer',
            'read'            => 'required|boolean',
            'sent'            => 'required|boolean',
        ];
        $rules = array_merge(
            $rules,  $this->method() == 'send'
            ? [
                'user_ids' => 'nullable|array',
                'dept_ids' => 'nullable|array',
                'app_ids'  => 'required|array',
            ]
            : [
                'content' => 'required|string',
            ]
        );
        $this->batchRules($rules);
        
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
    
        if (!($this->method() == 'update' && !Request::route('id'))) {
            $input = $this->all();
            $input['comm_type_id'] = $input['type'] == 'sms'
                ? CommType::whereName('短信')->first()->id
                : CommType::whereName('应用')->first()->id;
            $input['app_id'] = 0;
            $input['msl_id'] = 0;
            $input['title'] = MessageType::find($input['message_type_id'])->name
                . '(' . Constant::INFO_TYPE[$input['type']] . ')';
            $input['serviceid'] = '0';
            $input['message_id'] = 0;
            $input['url'] = "http://";
            $input['media_ids'] = '0';
            $input['s_user_id'] = Auth::id() ?? 0;
            $input['r_user_id'] = '0';
            $input['read'] = 0;
            $input['sent'] = 0;
            $deptIds = $userIds = [];
            if (isset($input['targetIds'])) {
                $targetIds = explode(',', $input['targetIds']);
                foreach ($targetIds as $targetId) {
                    # paths[2] = user-[departmentId]-[userId]
                    $paths = explode('-', $targetId);
                    if (isset($paths[2])) {
                        $userIds[] = $paths[2];
                    } else {
                        $deptIds[] = $targetId;
                    }
                }
                $input['user_ids'] = array_unique($userIds);
                $input['dept_ids'] = array_unique($deptIds);
            }
            # 定时发送的日期时间
            if (isset($input['time'])) {
                $input['time'] = $input['time'] . ':00';
            }
            if (!isset($input['app_ids'])) {
                $schoolId = $this->schoolId() ?? session('schoolId');
                $corp = School::find($schoolId)->corp;
                $input['app_ids'] = [
                    App::whereName('消息中心')->where('corp_id', $corp->id)->first()->id,
                ];
            }
            if (!isset($input['user_ids'])) {
                $input['user_ids'] = [];
            }
            if (!isset($input['dept_ids'])) {
                $input['dept_ids'] = [];
            }
            $action = explode('/', Request::path())[1];
            if ($action != 'send') {
                # 保存草稿
                $schoolId = $this->schoolId() ?? session('schoolId');
                $corp = School::find($schoolId)->corp;
                # 消息草稿的用户类发送对象只需保存学生和教职员工的userid
                $userids = User::whereIn('id', $input['user_ids'])
                    ->pluck('userid')->toArray();
                $agentid = $input['type'] != 'sms'
                    ? App::whereCorpId($corp->id)->where('name', '消息中心')->first()->agentid
                    : 0; # agentid 对短信类消息不适用
                $input['content'] = json_encode([
                    'touser'       => implode('|', $userids),
                    'toparty'      => implode('|', $input['dept_ids']),
                    'agentid'      => $agentid,
                    'msgtype'      => $input['type'],
                    $input['type'] => $input[$input['type']],
                ]);
            }
            # 需要立即或定时发送的消息中对应的用户类发送对象，
            # 需在Message表中保存学生对应的监护人userid及教职员工的userid，
            # 学生userid转换成监护人userid的过程将在SendMessage队列任务中进行
            $this->replace($input);
        }
        
    }
    
}
