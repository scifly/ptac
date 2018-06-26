<?php
namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Media 媒体
 *
 * @mixin Eloquent
 * @property int $id
 * @property string $path 媒体文件路径
 * @property string $remark 媒体文件备注
 * @property int $media_type_id 媒体类型ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read MediaType $mediaType
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
    function mediaType() { return $this->belongsTo('App\Models\MediaType'); }
    
    /**
     * 返回对应的网站模块对象
     *
     * @return HasOne
     */
    function wapSiteModule() { return $this->hasOne('App\Models\WapSiteModule'); }
    
    /**
     * 返回对应的网站文章对象
     *
     * @return HasOne
     */
    function wsmArticle() { return $this->hasOne('App\Models\WsmArticle'); }
    
    /**
     * 获取指定媒体所包含的所有菜单对象
     *
     * @return HasMany
     */
    function menus() { return $this->hasMany('App\Models\Menu'); }
    
    /**
     * 根据媒体ID返回媒体对象
     *
     * @param array $ids
     * @return array|null
     */
    function medias(array $ids) {
        
        $medias = null;
        foreach ($ids as $id) {
            $medias[] = $this->find($id);
        }
        
        return $medias;
        
    }
    
    /**
     * 保存媒体
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新媒体
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id = null) {
        
        return $id
            ? $this->find($id)->update($data)
            : $this->batch($this);
        
    }
    
    /**
     * 删除媒体
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定媒体的所有相关数据
     *
     * @param $id
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                (new WapSite)->removeMedia($id);
                (new WsmArticle)->removeMedia($id);
                WapSiteModule::whereMediaId($id)->update(['media_id' => 0]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 文件上传公共方法
     *
     * @param UploadedFile $file
     * @param string $remark
     * @return array|bool
     */
    function upload($file, $remark = '') {
        
        if ($file->isValid()) {
            # 文件名
            $filename = uniqid() . '-' . $file->getClientOriginalName();
            # 扩展名
            $ext = $file->getClientOriginalExtension();
            # 临时文件的绝对路径
            $realPath = $file->getRealPath();
            # image/jpeg/
            $type = self::mediaTypeId($file->getClientMimeType());
            // 上传文件
            // $filename = uniqid() . '.' . $ext;
            // 使用新建的uploads本地存储空间（目录）
            if (
            Storage::disk('uploads')->put(
                date('Y/m/d/', time()) . $filename,
                file_get_contents($realPath)
            )
            ) {
                $filePath = $this->uploadedFilePath($filename);
                $media = $this->create([
                    'path'          => $filePath,
                    'remark'        => $remark,
                    'media_type_id' => $type,
                    'enabled'       => Constant::ENABLED,
                ]);
                
                return [
                    'id'       => $media->id,
                    'path'     => $filePath,
                    'type'     => $ext,
                    'filename' => $filename,
                ];
            }
        }
        
        return null;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * @param $type
     * @return int
     */
    private function mediaTypeId($type) {
        
        switch (explode('/', $type)[0]) {
            case 'image':
                return 1;
            case 'audio':
                return 2;
            case 'video':
                return 3;
            case 'application':
                return 4;
            default:
                return 5;
        }
        
    }
    
}
