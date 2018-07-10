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
        
        return array_merge(
            $rules, explode('/', Request::path())[1] == 'send'
            ? [
                'user_ids' => 'nullable|array',
                'dept_ids' => 'nullable|array',
                'app_ids'  => 'required|array',
            ]
            : [
                'content' => 'required|string',
            ]
        );
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $input['comm_type_id'] = $input['type'] == 'sms'
            ? CommType::whereName('短信')->first()->id
            : CommType::whereName('应用')->first()->id;
        $input['app_id'] = 0;
        $input['msl_id'] = 0;
        $input['title'] = MessageType::find($input['message_type_id'])->name
            . '(' . Constant::INFO_TYPES[$input['type']] . ')';
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
            $schoolId = $this->schoolId() ?? session('schoolId');
            $corp = School::find($schoolId)->corp;
            if ($input['type'] == 'sms') {
                $input['content'] = $input['sms'];
            } else {
                $userids = User::whereIn('id', $input['user_ids'])
                    ->where('subscribed', 1)# 仅发送消息给已关注的用户
                    ->pluck('userid')->toArray();
                $data['content'] = json_encode([
                    'touser'       => implode('|', $userids),
                    'toparty'      => implode('|', $input['dept_ids']),
                    'agentid'      => App::whereCorpId($corp->id)->where('name', '消息中心')->first()->agentid,
                    'msgtype'      => $input['type'],
                    $input['type'] => $input[$input['type']],
                ]);
            }
            unset($input['user_ids'], $input['dept_ids'], $input['app_ids']);
        }
        $this->replace($input);
        
    }
    
}
