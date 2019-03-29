<?php
namespace App\Http\Requests;

use App\Helpers\{Constant, ModelTrait};
use App\Models\{CommType, MediaType, School, User};
use Illuminate\Foundation\Http\FormRequest;

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
        
        $rules = array_combine(Constant::MESSAGE_FIELDS, [
            'required|integer', 'required|integer',
            'required|integer', 'required|integer',
            'required|string|max:64', 'required|string',
            'required|string|max:255', 'required|integer',
            'required|string|max:255', 'required|string|max:255',
            'required|integer', 'required|integer',
            'required|integer', 'required|boolean',
            'required|boolean',
        ]);
        $rules = array_merge(
            $rules, $this->method() == 'send'
            ? ['user_ids' => 'nullable|array', 'dept_ids' => 'nullable|array']
            : ['content' => 'required|string']
        );
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    protected function prepareForValidation() {
        
        if (!($this->method() == 'update' && !$this->route('id'))) {
            $schoolId = $this->schoolId() ?? session('schoolId');
            $corp = School::find($schoolId)->corp;
            $app = $this->app($corp->id);
            $type = $this->input('type');
            $msgTypeId = $this->input('message_type_id');
            $input = array_combine(Constant::MESSAGE_FIELDS, [
                CommType::whereName($type == 'sms' ? '短信' : '微信')->first()->id,
                MediaType::whereName($type == 'sms' ? 'text' : $type)->first()->id,
                $app->id, 0, $this->title($type), '', '0', 0,
                'http://', '0', $this->user()->id ?? 0, 0, $msgTypeId, 0, 0,
            ]);
            $targetIds = $this->input('targetIds') ?? [];
            foreach (explode(',', $targetIds) as $targetId) {
                # paths[2] = user-[departmentId]-[userId]
                $paths = explode('-', $targetId);
                isset($paths[2])
                    ? $input['user_ids'][] = $paths[2]
                    : $input['dept_ids'][] = $targetId;
            }
            # 定时发送的日期时间
            $time = $this->input('time') ?? null;
            !$time ?: $input['time'] .= ':00';
            $input['user_ids'] = $input['user_ids'] ?? [];
            $input['dept_ids'] = $input['dept_ids'] ?? [];
            # 消息草稿的用户类发送对象只需保存学生和教职员工的userid
            $userids = User::whereIn('id', $input['user_ids'])->pluck('userid')->toArray();
            $input['content'] = json_encode([
                'touser'  => implode('|', $userids),
                'toparty' => implode('|', $input['dept_ids']),
                'agentid' => $type != 'sms' ? $app->agentid : 0,
                'msgtype' => $type,
                $type     => $this->input($type),
            ], JSON_UNESCAPED_UNICODE);
            
            # 需要立即或定时发送的消息中对应的用户类发送对象，
            # 需在Message表中保存学生对应的监护人userid及教职员工的userid，
            # 学生userid转换成监护人userid的过程将在SendMessage队列任务中进行
            $this->replace($input);
        }
        
    }
    
    /**
     * 生成消息title
     *
     * @param string $type
     * @return string
     */
    private function title(string $type) : string {
    
        switch ($type) {
            case 'text':
                $str = $this->input($type)['content'];
                break;
            case 'image':
            case 'voice':
            case 'file':
                $paths = explode('/', $this->input($type)['path']);
                $str = $paths[sizeof($paths) - 1];
                break;
            case 'video':
            case 'textcard':
                $str = $this->input($type)['title'];
                break;
            case 'mpnews':
                $str = $this->input($type)['articles'][0]['title'];
                break;
            case 'sms':
                $str = $this->input($type);
                break;
            default:
                break;
        }
        
        return mb_substr($str ?? '', 0, 64);
        
    }
    
}
