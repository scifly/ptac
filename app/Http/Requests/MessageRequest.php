<?php
namespace App\Http\Requests;

use App\Helpers\{ModelTrait};
use App\Models\{App, MediaType, Message, School, User};
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

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
    
    /** @return array */
    public function rules() {
        
        $rules = array_combine((new Message)->getFillable(), [
            'required|integer', 'required|integer',
            'required|integer', 'required|integer',
            'required|string|max:64', 'required|string',
            'required|string|max:255', 'required|integer',
            'required|url|max:255', 'required|integer',
            'required|integer', 'required|boolean',
            'required|boolean', 'nullable|integer'
        ]);
        $rules = array_merge(
            $rules, $this->method() == 'send'
            ? [
                'user_ids' => 'nullable|array',
                'dept_ids' => 'nullable|array',
                'tag_ids'  => 'nullable|array',
            ]
            : ['content' => 'required|string']
        );
        $this->batchRules($rules);
        
        return $rules;
        
    }
    
    /**
     * @throws Throwable
     */
    protected function prepareForValidation() {
        
        if (!($this->method() == 'update' && !$this->route('id'))) {
            $school = School::find($this->schoolId() ?? session('schoolId'));
            $app = $school->app ?? $this->corpApp($school->corp_id);
            $input = array_combine((new Message)->getFillable(), [
                $this->input('message_type_id'),
                MediaType::whereName($this->input('type'))->first()->id,
                $app->id, 0, $this->title($this->input('type')), '',
                uniqid(), 0, 'http://', $this->user()->id ?? 0, 0, 0, 0, null
            ]);
            if ($this->has('targetIds')) {
                # 从后台发送消息
                $targetIds = $this->input('targetIds') ?? [];
                is_array($targetIds) ?: $targetIds = explode(',', $targetIds);
                foreach ($targetIds as $targetId) {
                    # paths[2] = user-[departmentId]-[userId]
                    $paths = explode('-', $targetId);
                    isset($paths[2])
                        ? $input['user_ids'][] = $paths[2]
                        : $input['dept_ids'][] = $targetId;
                }
            } else {
                # 从微信端发送消息
                $input['user_ids'] = $this->input('user_ids');
                $input['dept_ids'] = $this->input('dept_ids');
            }
            # 定时发送的日期时间
            $time = $this->input('time') ?? null;
            !$time ?: $input['time'] .= ':00';
            $input['content'] = json_encode(
                $this->content($input, $app),
                JSON_UNESCAPED_UNICODE
            );
            # 需要立即或定时发送的消息中对应的用户类发送对象，
            # 需在Message表中保存学生对应的监护人userid及教职员工的userid，
            # 学生userid转换成监护人userid的过程将在SendMessage队列任务中进行
            $this->replace($input);
        }
        
    }
    
    /**
     * 获取消息标题
     *
     * @param string $type
     * @return string
     */
    private function title(string $type): string {
        
        $content = $this->input($type);
        switch ($type) {
            case 'image':
            case 'voice':
            case 'file':
                $paths = explode('/', $content['path']);
                $str = $paths[sizeof($paths) - 1];
                break;
            case 'video':
            case 'textcard':
                $str = $content['title'];
                break;
            case 'text':
                $str = $content['content'];
                break;
            case 'mpnews':
                $str = $content['articles'][0]['title'];
                break;
            default:    # sms
                $str = $content;
                break;
        }
        
        return mb_substr($str, 0, 64);
        
    }
    
    /**
     * 格式化消息内容
     *
     * @param $input
     * @param App $app
     * @return array
     */
    private function content($input, App $app) {
    
        # 消息草稿的用户类发送对象只需保存学生和教职员工的userid
        $userids = User::whereIn('id', $input['user_ids'] ?? []);
        $content = [
            'touser'  => $userids->pluck('userid')->join('|'),
            'toparty' => join('|', $input['dept_ids'] ?? []),
            'totag'   => join('|', $input['tag_ids'] ?? []),
            'msgtype' => $type = $this->input('type'),
            $type  => $this->input($type)
        ];
        if ($app->category  == 1) {
            $content['agentid'] = $app->agentid;
        } else {
            $content['template_id'] = $this->input('template_id');
        }
        
        return $content;
        
    }
    
}
