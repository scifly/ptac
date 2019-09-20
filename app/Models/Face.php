<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use App\Jobs\FaceConfig;
use Eloquent;
use Form;
use Html;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\{Carbon, Facades\Auth, Facades\Request};
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
 * @property-read int|null $cameras_count
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
    
    protected $fillable = ['user_id', 'media_id', 'state'];
    
    /** @return BelongsToMany */
    function cameras() { return $this->belongsToMany('App\Models\Camera', 'camera_face'); }
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
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
                    return '<img class="img-circle" style="height:32px;" src="' .
                        (!empty($d) ? '/' . $d : '/img/default.png') . '"> ';
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
                    $colors = [
                        ['text-gray', '未设置'],
                        ['text-green', '白名单'],
                        ['text-red', '黑名单'],
                        ['text-orange', 'VIP'],
                    ];
                    $state = $this->badge($colors[$d ?? 0][0], $colors[$d ?? 0][1]);
                    [$config, $remove] = array_map(
                        function ($prefix, $title, $style) use ($row) {
                            return $this->anchor($prefix . $row['id'], $title, $style);
                        }, ['cfg_', ''], ['设置', '删除'],
                        ['fa-pencil', 'fa-remove text-red']
                    );
                    $user = Auth::user();
    
                    return $state
                        . (($user->can('act', $this->uris()['create'])) ? $config : '')
                        . ($d ? (($user->can('act', $this->uris()['destroy'])) ? $remove : '') : '');
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
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function config() {
        
        FaceConfig::dispatch(
            Request::input('faces'), Auth::id()
        );
        
        return response()->json([
            'title' => __('messages.face.title'),
            'message' => __('messages.face.config_started')
        ]);
        
    }
    
    /**
     * 清除人脸识别数据
     *
     * @param null $userId
     * @return bool
     */
    function remove($userId = null) {
        
        $userIds = $userId ? [$userId] : Request::input('ids');
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            $faces[$userId] = [
                'cameraids' => $user->face->cameras->pluck('id')->toArray()
            ];
        }
        FaceConfig::dispatch(
            $faces ?? [], Auth::id()
        );
        
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
            Constant::NOT_ACCEPTABLE,
            __('messages.empty_file')
        );
        $uploadedFile = (new Media)->import(
            $file, __('messages.wap_site_module.title')
        );
        abort_if(
            !$uploadedFile,
            Constant::INTERNAL_SERVER_ERROR,
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
        $image = $media ? Html::image('../../' . $media->path, null, ['height' => 32])->toHtml() : '';
        $preview = Form::hidden(
                $id = 'media-id-' . $uid,
                $media ? $media->id : null,
                ['id' => $id, 'class' => 'medias']
            )->toHtml() . $image;
        # 上传/删除
        $upload = Html::tag('i', '', [
            'class' => 'fa fa-cloud-upload', 'title' => '上传'
        ])->toHtml();
        $remove = Html::tag('i', '', [
            'class' => 'fa fa-remove text-red',
            'title' => '删除',
            'style' => 'margin-left: 5px; display: ' . ($media ? 'inline' : 'none')
        ])->toHtml();
        $actions = Form::label(
            'face-' . $uid, join([$upload, $remove]),
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
        
        $selected = ($face = $user->face) ? $face->cameras->pluck('id')->toArray() : null;
        $id = 'cameraids-' . $user->id;

        return Form::select($id . '[]', $cameras, $selected, [
            'id' => $id,
            'multiple' => 'multiple',
            'class' => 'form-control select2',
            'style' => 'width: 100%;'
        ])->toHtml();
        
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
    function exists($mediaId) {
        
        abort_if(
            $this->whereMediaId($mediaId)->first() ? true : false,
            Constant::NOT_ACCEPTABLE,
            __('mediaId:' . $mediaId . ' 已被使用')
        );
        
    }
    
}
