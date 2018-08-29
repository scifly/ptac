<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\Wechat\WXBizMsgCrypt;
use App\Http\Controllers\Controller;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\DepartmentUser;
use App\Models\Educator;
use App\Models\Group;
use App\Models\Mobile;
use App\Models\School;
use App\Models\User;
use Doctrine\Common\Inflector\Inflector;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 微信考勤
 *
 * Class AttendanceController
 * @package App\Http\Controllers\Wechat
 */
class SyncController extends Controller {
    
    const MEMBER_PROPERTIES = [
        'NewUserID'   => 'userid',
        'Name'        => 'realname',
        'Position'    => 'position',
        'Gender'      => 'gender',
        'Email'       => 'email',
        'Status'      => 'subscribed',
        'Avatar'      => 'avatar_url',
        'EnglishName' => 'english_name',
        'IsLeader'    => 'isleader',
        'Telephone'   => 'telephone',
    ];
    protected $corp, $event, $schools, $schoolDepartmentIds;
    
    function __construct() {
        
        $paths = explode('/', Request::path());
        $this->corp = Corp::whereAcronym($paths[0])->first();
        $this->schools = $this->corp->schools;
        $this->schoolDepartmentIds = $this->schools->pluck('department_id')->toArray();
        
    }
    
    /**
     * 接收通讯录变更事件
     *
     * @return string
     * @throws Throwable
     */
    public function sync() {
        
        // $this->verifyUrl();
        // exit;
        try {
            DB::transaction(function () {
                $this->event = $this->event();
                $type = $this->event->{'Event'};
                switch ($type) {
                    case 'subscribe':       # 关注
                        User::whereUserid($this->event->{'FromUserName'})->first()->update([
                            'avatar_url' => $this->member()->{'avatar'},
                            'subscribed' => 1,
                        ]);
                        break;
                    case 'unsubscribe':     # 取消关注
                        User::whereUserid($this->event->{'FromUserName'})->first()->update([
                            'subscribed' => 0,
                        ]);
                        break;
                    case 'change_contact':  # 通讯录变更
                        $changeType = $this->event->{'ChangeType'};
                        $this->{Inflector::camelize($changeType)}();
                        break;
                    default:
                        break;
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return '';
        
    }
    
    /**
     * 创建会员
     *
     * @throws Throwable
     */
    protected function createUser() {
    
        # 创建用户(教职员工)
        $data = $this->data();
        $data['username'] = $data['userid'];
        $data['password'] = bcrypt('12345678');
        $data['synced'] = 1;
        $data['enabled'] = 1;
        $role = $this->role();
        $school = null;
        switch ($role) {
            case 'educator':
                $school = $this->school();
                $data['group_id'] = Group::where([
                    'name' => '教职员工',
                    'school_id' => $school->id
                ])->first()->id;
                break;
            case 'school':
                $data['group_id'] = Group::whereName('学校')->first()->id;
                break;
            case 'corp':
                $data['group_id'] = Group::whereName('企业')->first()->id;
                break;
            default:
                break;
        }
        $user = User::create($data);
        if ($role != 'corp') {
            # 创建教职员工
            Educator::create([
                'user_id'   => $user->id,
                'school_id' => $school->id,
                'sms_quote' => 0,
                'enabled'   => 1
            ]);
            $departmentIds = (array) $this->event->{'Department'};
        } else {
            $departmentIds = [$this->corp->department_id];
        }
        # 创建部门&用户绑定关系
        (new DepartmentUser)->storeByUserId($user->id, $departmentIds);
        # 保存用户手机号码
        Mobile::create([
            'user_id' => $user->id,
            'mobile' => $this->event->{'Mobile'},
            'isdefault' => 1,
            'enabled' => 1
        ]);
        
    }
    
    /**
     * 更新会员
     *
     * @throws Throwable
     */
    protected function updateUser() {
    
        # 更新用户
        $user = User::whereUserid($this->event->{'UserID'})->first();
        if (!$user) {
            $this->createUser();
        } else {
            $user->update($this->data());
            # 更新用户手机号码
            if (property_exists($this->event, 'Mobile')) {
                Mobile::where(['user_id' => $user->id, 'isdefault' => 1])
                    ->first()->update(['mobile' => $this->event->{'Mobile'}]);
            }
            # 更新用户所属部门
            if (property_exists($this->event, 'Department')) {
                $du = new DepartmentUser;
                $du->where('user_id', $user->id)->delete();
                $role = $this->role();
                $departmentIds = (array) $this->event->{'Department'};
                switch ($role) {
                    case 'educator':
                        $user->update([
                            'group_id' => Group::where([
                                'name' => '教职员工',
                                'school_id' => $this->school()->id
                            ])
                        ]);
                        break;
                    case 'school':
                        $user->update(['group_id' => Group::whereName('学校')->first()->id]);
                        break;
                    case 'corp':
                        $user->update(['group_id' => Group::whereName('企业')->first()->id]);
                        $departmentIds = [$this->corp->department_id];
                        break;
                    default:
                        break;
                }
                $du->storeByUserId($user->id, $departmentIds);
            }
        }
        
    }
    
    /**
     * 删除会员
     *
     * @throws Throwable
     */
    protected function deleteUser() {
    
        $userId = User::whereUserid($this->event->{'UserID'})->first()->id;
        (new User)->remove($userId, false);
        
    }
    
    /**
     * 创建部门
     */
    protected function createParty() {
    
        $data = [
            'id' => $this->event->{'Id'},
            'name' => $this->event->{'Name'},
            'parent_id' => $this->event->{'ParentId'},
            'department_type_id' => DepartmentType::whereName('其他')->first()->id,
            'enabled' => 1
        ];
        Department::create($data);
        
    }
    
    /**
     * 更新部门
     */
    protected function updateParty() {
    
        $department = Department::find($this->event->{'Id'});
        if (!$department) {
            $this->createParty();
        } else {
            $department->update([
                'name' => $this->event->{'Name'},
                'parent_id' => $this->event->{'ParentId'}
            ]);
        }
        
    }
    
    /**
     * 删除部门
     *
     * @throws Throwable
     */
    protected function deleteParty() {
    
        (new Department)->remove($this->event->{'Id'});
        
    }
    
    /**
     * 更新标签
     */
    protected function updateTag() {
    
    
    
    }
    
    /**
     * 验证回调Url
     */
    protected function verifyUrl() {
        
        $paths = explode('/', Request::path());
        $corp = Corp::whereAcronym($paths[0])->first();
        // 假设企业号在公众平台上设置的参数如下
        $encodingAesKey = $corp->encoding_aes_key;
        $token = $corp->token;
        $corpId = $corp->corpid;
        $sVerifyMsgSig = Request::query('msg_signature');
        $sVerifyTimeStamp = Request::query('timestamp');
        $sVerifyNonce = Request::query('nonce');
        $sVerifyEchoStr = rawurldecode(Request::query('echostr'));
        // 需要返回的明文
        $sEchoStr = "";
        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
        $errCode = $wxcpt->VerifyURL(
            $sVerifyMsgSig,
            $sVerifyTimeStamp,
            $sVerifyNonce,
            $sVerifyEchoStr,
            $sEchoStr
        );
        echo !$errCode ? $sEchoStr : "ERR: " . $errCode . "\n\n";
        
    }
    
    /**
     * 返回企业微信推送的事件对象
     *
     * @return int|\SimpleXMLElement
     */
    private function event() {
        
        $wxcpt = new WXBizMsgCrypt(
            $this->corp->token,
            $this->corp->encoding_aes_key,
            $this->corp->corpid
        );
        $msgSignature = Request::query('msg_signature');
        $timestamp = Request::query('timestamp');
        $nonce = Request::query('nonce');
        $content = '';
        $errcode = $wxcpt->DecryptMsg(
            $msgSignature,
            $nonce,
            Request::getContent(),
            $content,
            $timestamp
        );
        
        return $errcode
            ? $errcode
            : simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        
    }
    
    /**
     * 返回事件对应的会员对象
     *
     * @return mixed
     */
    private function member() {
        
        $userid = $this->event->{'UserID'};
        $token = Wechat::getAccessToken(
            $this->corp->corpid,
            $this->corp->contact_sync_secret,
            true
        );
        if ($token['errcode']) {
            return $token['errcode'];
        }
        $member = json_decode(Wechat::getUser($token['access_token'], $userid));
        
        return $member->{'errcode'} ? $member->{'errcode'} : $member;
        
    }
    
    /**
     * 从事件中提取并返回会员数据
     *
     * @return array
     */
    private function data() {
        
        $data = [];
        foreach (self::MEMBER_PROPERTIES as $property => $field) {
            if (property_exists($this->event, $property)) {
                $value = $this->event->{$property};
                if ($property == 'Gender') {
                    $value = $value == 1 ? 0 : 1;
                }
                if ($property == 'Status') {
                    $value = $value == 1 ? 1 : 0;
                }
                $data[$field] = $value;
            }
        }
        
        return $data;
        
    }
    
    /**
     * 返回会员角色
     *
     * @return string
     */
    private function role() {
        
        $department = new Department;
        $departmentIds = $this->event->{'Department'};
        $dIds = array_intersect($departmentIds, [1, 1175014494]);
        if (!empty($dIds)) { return 'corp'; }
        foreach ($this->schoolDepartmentIds as $schoolDepartmentId) {
            $schoolDepartmentIds = array_merge(
                $schoolDepartmentId,
                $department->subDepartmentIds($schoolDepartmentId)
            );
            $diffs = array_diff($departmentIds, $schoolDepartmentIds);
            if (empty($diffs)) {
                return in_array($schoolDepartmentId, $departmentIds) ? 'school' : 'educator';
            }
            if (sizeof($diffs) == sizeof($departmentIds)) {
                continue;
            }
            
            return 'corp';
        }
        
        return 'educator';
        
    }
    
    /**
     * 返回会员所属的学校对象
     *
     * @return School|Builder|Model|null|object
     */
    private function school() {
        
        $departmentId = head((array)$this->event->{'Department'});
        $schoolDepartmentId = (new Department)->departmentId($departmentId);
        
        return School::whereDepartmentId($schoolDepartmentId)->first();
        
    }
    
}