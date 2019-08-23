<?php
namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Member
 *
 * @package App\Models
 * @property int $id
 * @property int $group_id 所属角色/权限ID
 * @property int|null $card_id
 * @property int|null $face_id
 * @property string $username 用户名
 * @property string|null $remember_token "记住我"令牌，登录时用
 * @property string $password 密码
 * @property string|null $email 电子邮箱
 * @property int $gender 性别
 * @property string $realname 真实姓名
 * @property string|null $avatar_url 头像URL
 * @property string $userid 成员userid
 * @property string|null $english_name 英文名
 * @property int|null $isleader 上级字段，标识是否为上级。第三方暂不支持
 * @property string|null $position 职位信息
 * @property string|null $telephone 座机号码
 * @property int|null $order 部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $synced 是否已同步到企业号
 * @property int $subscribed 是否关注企业微信
 * @property int $enabled
 * @method static Builder|Member newModelQuery()
 * @method static Builder|Member newQuery()
 * @method static Builder|Member query()
 * @method static Builder|Member whereAvatarUrl($value)
 * @method static Builder|Member whereCardId($value)
 * @method static Builder|Member whereCreatedAt($value)
 * @method static Builder|Member whereEmail($value)
 * @method static Builder|Member whereEnabled($value)
 * @method static Builder|Member whereEnglishName($value)
 * @method static Builder|Member whereFaceId($value)
 * @method static Builder|Member whereGender($value)
 * @method static Builder|Member whereGroupId($value)
 * @method static Builder|Member whereId($value)
 * @method static Builder|Member whereIsleader($value)
 * @method static Builder|Member whereOrder($value)
 * @method static Builder|Member wherePassword($value)
 * @method static Builder|Member wherePosition($value)
 * @method static Builder|Member whereRealname($value)
 * @method static Builder|Member whereRememberToken($value)
 * @method static Builder|Member whereSubscribed($value)
 * @method static Builder|Member whereSynced($value)
 * @method static Builder|Member whereTelephone($value)
 * @method static Builder|Member whereUpdatedAt($value)
 * @method static Builder|Member whereUserid($value)
 * @method static Builder|Member whereUsername($value)
 * @mixin Eloquent
 */
class Member extends Model {
    
    //
}
