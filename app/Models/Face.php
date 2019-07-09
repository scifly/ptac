<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait, Snippet};
use App\Jobs\FaceConfig;
use Eloquent;
use Exception;
use Form;
use Html;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasOne};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\{Carbon, Facades\Auth, Facades\DB, Facades\Request};
use Throwable;

/**
 * Class Face 人脸
 *
 * @package App\Models
 * @property int $id
 * @property int $user_id 用户id
 * @property string $media_id 人脸图片媒体id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $state 状态: 1 - 白名单，2 - 黑名单，3 - vip
 * @property-read User $user
 * @property-read Media $media
 * @property-read Collection|Camera[] $cameras
 * @method static Builder|Face newModelQuery()
 * @method static Builder|Face newQuery()
 * @method static Builder|Face query()
 * @method static Builder|Face whereCreatedAt($value)
 * @method static Builder|Face whereId($value)
 * @method static Builder|Face whereState($value)
 * @method static Builder|Face whereUpdatedAt($value)
 * @method static Builder|Face whereMediaId($value)
 * @method static Builder|Face whereUserId($value)
 * @mixin Eloquent
 */
class Face extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'faceid', 'user_id', 'v_type', 'v_start',
        'v_end', 'wgid', 'url', 'state',
    ];
    
    /**
     * 返回指定人脸所属的所有人脸识别设备对象
     *
     * @return BelongsToMany
     */
    function cameras() {
        
        return $this->belongsToMany('App\Models\Camera', 'camera_face');
        
    }
    
    /**
     * 返回人脸所属用户对象
     *
     * @return HasOne
     */
    function user() { return $this->hasOne('App\Models\User'); }
    
    /**
     * 返回热恋所属的媒体对象
     *
     * @return BelongsTo
     */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 人脸数据列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'User.id', 'dt' => 0],
            [
                'db'        => 'Media.path', 'dt' => 1,
                'formatter' => function ($d) {
                    return isset($d) ? Snippet::avatar($d) : ' - ';
                },
            ],
            ['db' => 'User.realname', 'dt' => 2],
            ['db' => 'Groups.name', 'dt' => 3],
            [
                'db'        => 'Face.created_at', 'dt' => 4, 'dr' => true,
                'formatter' => function ($d) { return $d ?? ' - '; },
            ],
            [
                'db'        => 'Face.updated_at', 'dt' => 5, 'dr' => true,
                'formatter' => function ($d) { return $d ?? ' - '; },
            ],
            [
                'db'        => 'Face.state', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    // if (!isset($d)) return ' - ';
                    $colors = [
                        ['text-gray', '未设置'],
                        ['text-green', '白名单'],
                        ['text-red', '黑名单'],
                        ['text-orange', 'VIP'],
                    ];
                    $state = sprintf(
                        Snippet::BADGE,
                        $colors[$d ?? 0][0], $colors[$d ?? 0][1]
                    );
                    
                    return $state; // Datatable::status($state, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'faces',
                'alias'      => 'Face',
                'type'       => 'LEFT',
                'conditions' => [
                    'Face.id = User.face_id',
                ],
            ],
            [
                'table'      => 'groups',
                'alias'      => 'Groups',
                'type'       => 'INNER',
                'conditions' => [
                    'Groups.id = User.group_id',
                ],
            ],
            [
                'table'      => 'medias',
                'alias'      => 'Media',
                'type'       => 'LEFT',
                'conditions' => [
                    'Media.id = Face.media_id',
                ],
            ],
        ];
        $condition = 'User.id IN (' . $this->visibleUserIds() . ')';
        
        return Datatable::simple(new User, $columns, $joins, $condition);
        
    }
    
    /**
     * 设置人脸识别 - (批量)设置、修改、清除
     *
     * @param User|null $user
     * @param bool $api - 是否同步
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function store(User $user = null, $api = true) {
        
        try {
            DB::transaction(function () use ($user, $api) {
                !$user ?: Request::merge([
                    'faces' => [$user->id => Request::input('face')],
                ]);
                $faces = Request::input('faces');
                $inserts = $replaces = $purges = $userIds = [];
                foreach ($faces as $userId => $face) {
                    if (!empty($face)) {
                        if (!$_face = Face::whereUserId($userId)->first()) {
                            $inserts[] = $face;
                            $userIds[] = $userId;
                        } elseif ($_face->media_id != $face['media_id']) {
                            $this->exists($face['media_id']);
                            $replaces[$userId] = $face;
                        }
                    } else {
                        !User::find($userId)->face ?: $purges[] = $userId;
                    }
                }
                # 新增
                foreach ($inserts as $insert) {
                    $face = $this->create($insert);
                    $face->user->update(['face_id' => $face->id]);
                    (new CameraFace)->storeByFaceId($face->id, $insert['cameraids']);
                }
                # 修改
                $input['faces'] = $replaces;
                Request::replace($input);
                $this->modify(false);
                # 清除
                Request::merge(['ids' => $purges]);
                $this->remove(false);
                # 同步
                !$api ?: FaceConfig::dispatch(
                    [array_keys($inserts), array_keys($replaces), $purges],
                    Auth::id()
                );
            });
            
        } catch (Exception $e) {
            throw $e;
        }
        
        return !$api ? true : response()->json([
            'title'   => '批量设置人脸识别',
            'message' => __('messages.ok'),
        ]);
        
    }
    
    /**
     * 修改设置
     *
     * @param bool $api - 是否同步
     * @return bool
     * @throws Throwable
     */
    function modify($api = true) {
        
        $faces = Request::input('faces');
        try {
            DB::transaction(function () use ($faces, $api) {
                $inserts = $replaces = $purges = [];
                $cf = new CameraFace;
                foreach ($faces as $userId => $face) {
                    $user = User::find($userId);
                    if ($mediaId = $face['media_id']) {
                        if ($user->face && $mediaId != $user->face->media_id) {
                            $user->face->update($face); # 修改
                            $cf->storeByFaceId($user->face_id, $face['cameraids']);
                            $replaces[] = $userId;
                        } elseif (!$user->face) {
                            $inserts[$userId] = $face;
                        }
                    } elseif ($user->face) {
                        $purges[] = $userId;
                    }
                }
                # 新增
                $input['faces'] = $inserts;
                Request::replace($input);
                $this->store(null, false);
                # 删除
                Request::merge(['ids' => $purges]);
                $this->remove(false);
                # 同步
                !$api ?: FaceConfig::dispatch([$inserts, $replaces, $purges], Auth::id());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 清除人脸识别数据
     *
     * @param bool $api - 是否同步
     * @return bool
     * @throws Throwable
     */
    function remove($api = true) {
        
        try {
            DB::transaction(function () use ($api) {
                $userIds = (Request::route('id') && stripos(Request::path(), 'delete') !== false)
                    ? [Request::route('id')]
                    : array_values(Request::input('ids'));
                # 同步
                !$api ?: FaceConfig::dispatch([[], [], $userIds], Auth::id());
                # 删除
                $users = User::whereIn('id', $userIds);
                CameraFace::whereIn('face_id', $users->pluck('face_id')->toArray())->delete();
                $this->whereIn('user_id', $userIds)->delete();
                $users->update(['face_id' => 0]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 上传应用模块图标
     *
     * @return JsonResponse
     */
    function import() {
        
        $file = Request::file('file');
        abort_if(
            empty($file),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.empty_file')
        );
        $uploadedFile = (new Media)->import(
            $file, __('messages.wap_site_module.title')
        );
        abort_if(
            !$uploadedFile,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        
        return response()->json($uploadedFile);
        
    }
    
    /**
     * 人脸照片上传html
     *
     * @param User $user
     * @return string
     */
    function uploader(User $user) {
        
        $media = $user->face ? $user->face->media : null;
        $uid = $user->id;
        # 图片预览
        $image = $media ? Html::image('../../' . $media->path)->toHtml() : '';
        $preview = Form::hidden(
            $id = 'media-id-' . $uid,
            $media ? $media->id : null,
            ['id' => $id, 'class' => 'medias']
        )->toHtml() . $image;
        # 上传/删除
        $upload = '<i class="fa fa-cloud-upload" title="上传"></i>';
        $remove = '<i class="fa fa-remove text-red" title="删除" style="margin-left:5px; display: '
            . ($media ? 'block' : 'none') . '"></i>';
        $actions = Form::label(
            'face-' . $uid, $upload . $remove,
            ['class' => 'custom-file-upload text-blue'], false
        )->toHtml();
        # 上传控件
        $uploader = Form::file('face-' . $uid, [
            'id'     => 'face-' . $uid,
            'accept' => 'image/*',
            'class'  => 'face-upload',
        ])->toHtml();
        
        return sprintf(
            '<div class="preview-%s">%s</div>%s%s',
            $uid, $preview, $actions, $uploader
        );
        
    }
    
    /**
     * 设备选择下拉列表html
     *
     * @param array $cameras
     * @param User $user
     * @return string
     */
    function selector(array $cameras, User $user) {
        
        $face = $user->face;
        $selected = $face ? $face->cameras->pluck('id')->toArray() : null;
        $options = '';
        foreach ($cameras as $key => $value) {
            $isSelected = array_key_exists($key, $selected ?? []) ? 'selected>' : '>';
            $options .= '<option value="' . $key . '" ' . $isSelected . $value . '</option>';
        }
        $id = 'cameraids-' . $user->id;
        $name = $id . '[]';
        $tpl = Html::tag('select', '%s', [
            'multiple' => 'multiple', 'name' => '%s',
            'id' => '%s', 'class' => 'form-control select2',
            'style' => '%s'
        ])->toHtml();

        return sprintf($tpl, $name, $id, 'width: 100%;', $options);
        
    }
    
    /**
     * 人脸状态下拉列表html
     *
     * @param $selected
     * @param $userId
     * @return string
     */
    function state($selected, $userId) {
        
        $options = [1 => '白名单', 2 => '黑名单', 3 => 'VIP'];
        
        return Form::select('state', $options, $selected, [
            'id'       => 'state-' . $userId,
            'class'    => 'form-control select2 input-sm',
            'style'    => 'width: 100%;',
            'disabled' => sizeof($options) <= 1,
        ])->toHtml();
        
    }
    
    /**
     * 获取时间字符串(Ymd H:i:s, 星期X, H:i:s)
     *
     * @param $d
     * @param $row
     * @return false|string
     */
    /*private function range($d, $row) {
        
        switch ($row['v_type']) {
            case 0:
                return date('Ymd H:i:s', $d);
            case 1:
                return Constant::WEEK_DAYS[$d];
            case 2:
                return gmdate('H:i:s', $d);
            default:
                return 'n/a';
        }
        
    }*/
    /**
     * 判断媒体图片是否已存在
     *
     * @param $mediaId
     */
    private function exists($mediaId) {
        
        abort_if(
            $this->whereMediaId($mediaId)->first() ? true : false,
            HttpStatusCode::NOT_ACCEPTABLE,
            __('mediaId:' . $mediaId . ' 已被使用')
        );
        
    }
    
}
