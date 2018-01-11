<?php

namespace App\Models;

use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Media 媒体
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $path 媒体文件路径
 * @property string $remark 媒体文件备注
 * @property int $media_type_id 媒体类型ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property-read \App\Models\MediaType $mediaType
 * @method static Builder|Media whereCreatedAt($value)
 * @method static Builder|Media whereEnabled($value)
 * @method static Builder|Media whereId($value)
 * @method static Builder|Media whereMediaTypeId($value)
 * @method static Builder|Media wherePath($value)
 * @method static Builder|Media whereRemark($value)
 * @method static Builder|Media whereUpdatedAt($value)
 * @property-read WapSiteModule $wapsitemoudle
 * @property-read WsmArticle $wasmarticle
 * @property-read Collection|Menu[] $menus
 * @property-read WapSiteModule $wapSiteModule
 * @property-read WsmArticle $wsmArticle
 */
class Media extends Model {

    use ModelTrait;

    protected $table = 'medias';

    protected $fillable = [
        'path', 'remark', 'media_type_id', 'enabled',
    ];

    /**
     * 返回指定媒体所属的媒体类型对象
     *
     * @return BelongsTo
     */
    public function mediaType() { return $this->belongsTo('App\Models\MediaType'); }
    
    /**
     * 返回对应的网站模块对象
     *
     * @return HasOne
     */
    public function wapSiteModule() { return $this->hasOne('App\Models\WapSiteModule'); }
    
    /**
     * 返回对应的网站文章对象
     *
     * @return HasOne
     */
    public function wsmArticle() { return $this->hasOne('App\Models\WsmArticle'); }

    /**
     * 获取指定媒体所包含的所有菜单对象
     *
     * @return HasMany
     */
    public function menus() { return $this->hasMany('App\Models\Menu'); }

    /**
     * 根据媒体ID返回媒体对象
     *
     * @param array $ids
     * @return array
     */
    static function medias(array $ids) {

        $medias = [];
        foreach ($ids as $id) {
            $medias[] = self::find($id);
        }

        return $medias;

    }

    /**
     * 保存媒体
     *
     * @param array $data
     * @return bool
     */
    static function store(array $data) {

        $media = self::create($data);

        return $media ? true : false;

    }

    /**
     * 更新媒体
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    static function modify(array $data, $id) {

        $media = self::find($id);
        if (!$media) { return false; }

        return $media->update($data) ? true : false;

    }
    
    /**
     * 删除删除媒体
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    static function remove($id) {

        $media = self::find($id);
        if (!$media) { return false; }
        
        return $media->removable($media) ? $media->delete() : false;

    }


    /**
     * 文件上传公共方法
     *
     * @param UploadedFile $file
     * @param int $remark
     * @return array|bool
     */
    static function upload($file, $remark = 0) {

        if ($file->isValid()) {
            // 获取文件相关信息
            # 文件原名
            $originalName = $file->getClientOriginalName();
            # 扩展名
            $ext = $file->getClientOriginalExtension();
            # 临时文件的绝对路径
            $realPath = $file->getRealPath();
            # image/jpeg/
            $type = self::mediaTypeId($file->getClientMimeType());
            // 上传文件
            $filename = uniqid() . '.' . $ext;
            // 使用新建的uploads本地存储空间（目录）
            if (Storage::disk('uploads')->put($filename, file_get_contents($realPath))) {
                $filePath = 'public/uploads/' .
                    date('Y') . '/' .
                    date('m') . '/' .
                    date('d') . '/' .
                    $filename;
                $mediaId = Media::insertGetId([
                    'path'          => $filePath,
                    'remark'        => $remark,
                    'media_type_id' => $type,
                    'enabled'       => '1',
                ]);

                return [
                    'id'       => $mediaId,
                    'path'     => $filePath,
                    'type'     => $ext,
                    'filename' => $originalName,
                ];
            } else {
                return false;
            }
        }

        return false;

    }

    /**
     * @param $type
     * @return int
     */
    private static function mediaTypeId($type) {

        switch (explode('/', $type)[0]) {
            case 'image': return 1;
            case 'audio': return 2;
            case 'video': return 3;
            case 'application': return 4;
            default: return 5;
        }

    }

}
