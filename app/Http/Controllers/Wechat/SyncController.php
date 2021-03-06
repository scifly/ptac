<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\{Wechat\WXBizMsgCrypt};
use App\Http\Controllers\Controller;
use App\Models\{Corp, Department, DepartmentType, DepartmentUser, Educator, Group, Mobile, School, User};
use Doctrine\Common\Inflector\Inflector;
use Exception;
use Illuminate\Support\Facades\{DB, Request};
use SimpleXMLElement;
use Throwable;

/**
 * 通讯录变更同步
 *
 * Class SyncController
 * @package App\Http\Controllers\Wechat
 */
class SyncController extends Controller {
    
    static $category = 1;
    
    const MEMBER_PROPERTIES = [
        'Name'        => 'realname',
        'Gender'      => 'gender',
        'Email'       => 'email',
        'Mobile'      => 'mobile',
        'UserID'      => 'userid',
        'Position'    => 'position',
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
        $this->schools = $this->corp ? $this->corp->schools : null;
        $this->schoolDepartmentIds = !$this->schools ? []
            : $this->schools->pluck('department_id')->toArray();
        
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
                if (in_array($type, ['subscribe', 'unsubscribe'])) {
                    $data = ['subscribed' => $type == 'subscribe' ? 1 : 0];
                    if ($type == 'subscribe') $data['avatar_url'] = $this->member()->{'avatar'};
                    User::whereUserid($this->event->{'FromUserName'})->first()->update($data);
                } elseif ($type == 'change_contact') {
                    $this->{Inflector::camelize($this->event->{'ChangeType'})}();
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
        
        try {
            DB::transaction(function () {
                $data = $this->data();
                $groupId = $this->groupId();
                $data['username'] = $data['userid'];
                $data['group_id'] = $groupId;
                $data['password'] = bcrypt('12345678');
                $data['mobile'] = $data['mobile'] ?? '';
                $data['ent_attrs'] = [
                    'synced' => $data['enabled'] = 1
                ];
                $user = User::create($data);
                $departmentIds = [$this->corp->department_id];
                if (Group::find($groupId)->name != '企业') {
                    $deptId = head((array)$this->event->{'Department'});
                    Educator::create(
                        array_combine(
                            (new Educator)->getFillable(),
                            [$user->id, $this->school($deptId)->id, 0, 0, 1]
                        )
                    );
                    $departmentIds = (array)$this->event->{'Department'};
                }
                (new DepartmentUser)->storeByUserId($user->id, $departmentIds);
                Mobile::create(array_combine(
                    (new Mobile)->getFillable(),
                    [$user->id, $this->event->{'Mobile'}, 1, 1]
                ));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 更新会员
     *
     * @throws Throwable
     */
    protected function updateUser() {
        
        try {
            DB::transaction(function () {
                # 更新用户
                if (!($user = User::whereUserid($this->event->{'UserID'})->first())) {
                    $this->createUser();
                } else {
                    $user->update($this->data());
                    # 更新用户手机号码
                    !property_exists($this->event, 'Mobile')
                        ?: User::find($user->id)->update([
                            'mobile' => $this->event->{'Mobile'}
                        ]);
                    # 更新用户所属部门
                    if (property_exists($this->event, 'Department')) {
                        $du = new DepartmentUser;
                        $du->where(['user_id' => $user->id, 'enabled' => 1])->delete();
                        $groupId = $this->groupId();
                        $user->update(['group_id' => $groupId]);
                        $departmentIds = Group::find($groupId)->first()->name == '企业'
                            ? [$this->corp->department_id]
                            : (array)$this->event->{'Department'};
                        $du->storeByUserId($user->id, $departmentIds);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 删除会员
     *
     * @throws Throwable
     */
    protected function deleteUser() {
        
        !($user = User::whereUserid($this->event->{'UserID'})->first())
            ?: $user->purge($user->id);
        
    }
    
    /**
     * 创建部门
     */
    protected function createParty() {
        
        !Department::find($parentId = $this->event->{'ParentId'})
            ?: Department::create([
            'id'                 => $this->event->{'Id'},
            'name'               => $this->event->{'Name'},
            'parent_id'          => $parentId,
            'department_type_id' => DepartmentType::whereName('其他')->first()->id,
            'enabled'            => 1,
        ]);
        
    }
    
    /**
     * 更新部门
     */
    protected function updateParty() {
        
        list($department, $parent) = array_map(
            function ($name) {
                return Department::find($this->event->{$name});
            }, ['Id', 'ParentId']
        );
        !$department
            ? $this->createParty()
            : (!$parent ?: $department->{'update'}([
            'name'      => $this->event->{'Name'},
            'parent_id' => $parent->id,
        ]));
        
    }
    
    /**
     * 删除部门
     *
     * @throws Throwable
     */
    protected function deleteParty() {
        
        $id = $this->event->{'Id'};
        !($department = Department::find($id)) ?: $department->remove($id);
        
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
     * @return int|SimpleXMLElement
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
        
        return $errcode ? $errcode
            : simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        
    }
    
    /**
     * 返回事件对应的会员对象
     *
     * @return mixed
     * @throws Exception
     */
    private function member() {
        
        try {
            $token = Wechat::token(
                'ent', $this->corp->corpid,
                Wechat::syncSecret($this->corp->id)
            );
            $member = json_decode(
                Wechat::invoke(
                    'ent', 'user', 'get',
                    [$token], ['userid' => $this->event->{'UserID'}]
                ), true
            );
    
            return $member['errcode'] ?? $member;
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 从事件中提取并返回会员数据
     *
     * @return array
     */
    private function data() {
        
        foreach (self::MEMBER_PROPERTIES as $property => $field) {
            if (property_exists($this->event, $property)) {
                $value = $this->event->{$property};
                $property != 'Status' ?: ($value = $value == 1 ? 1 : 0);
                $data[$field] = $value;
            }
        }
        
        return $data ?? [];
        
    }
    
    /**
     * 返回会员角色
     *
     * @return string
     */
    private function groupId() {
        
        $dept = new Department;
        [$cGId, $sGId] = array_map(
            function ($name) {
                return Group::whereName($name)->first()->id;
            }, ['企业', '学校']
        );
        $deptIds = collect(explode(',', $this->event->{'Department'}));
        foreach ($this->schoolDepartmentIds as $schoolDeptId) {
            $schoolDeptIds = collect([$schoolDeptId])->merge(
                $dept->subIds($schoolDeptId)
            );
            if ($deptIds->diff($schoolDeptIds)->isEmpty()) {
                $deptId = head((array)$this->event->{'Department'});
                return $deptIds->has($schoolDeptId) ? $sGId
                    : Group::where([
                        'name' => '教职员工', 'school_id' => $this->school($deptId)->id,
                    ])->first()->id;
            }
        }
        
        return $cGId;
        
    }
    
    /**
     * 返回会员所属的学校对象
     *
     * @param $deptId
     * @param null $school
     * @return School
     */
    private function school($deptId, $school = null) {
    
        $dept = Department::find($deptId);
        if (!$dept->school) {
            $school = $this->school($dept->parent_id, $school);
        }
    
        return $school;
        
    }
    
}