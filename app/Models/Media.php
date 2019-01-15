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
use Throwable;

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
 * @property-read WapSiteModule $wapsitemoudle
 * @property-read WsmArticle $wasmarticle
 * @property-read Collection|Menu[] $menus
 * @property-read WapSiteModule $wapSiteModule
 * @property-read WsmArticle $wsmArticle
 * @method static Builder|Media whereCreatedAt($value)
 * @method static Builder|Media whereEnabled($value)
 * @method static Builder|Media whereId($value)
 * @method static Builder|Media whereMediaTypeId($value)
 * @method static Builder|Media wherePath($value)
 * @method static Builder|Media whereRemark($value)
 * @method static Builder|Media whereUpdatedAt($value)
 * @method static Builder|Media newModelQuery()
 * @method static Builder|Media newQuery()
 * @method static Builder|Media query()
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
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定媒体的所有相关数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $media = $this->find($id);
                (new WapSite)->removeMedia($id);
                (new WsmArticle)->removeMedia($id);
                WapSiteModule::whereMediaId($id)->update(['media_id' => 0]);
                Storage::disk('uploads')->delete($media->path);
                $media->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 文件上传公共方法
     *
     * @param UploadedFile $file
     * @param string $remark
     * @return array|bool
     */
    function import($file, $remark = '') {
        
        if ($file->isValid()) {
            $filename = uniqid() . '-' . $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $realPath = $file->getRealPath();
            $type = $this->mediaTypeId($file->getClientMimeType());
            $stored = Storage::disk('uploads')->put(
                date('Y/m/d/', time()) . $filename,
                file_get_contents($realPath)
            );
            if ($stored) {
                $filePath = $this->filePath($filename);
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
        
        $types = [
            'application', 'example', 'font',
            'message', 'model', 'multipart',
        ];
        $name = explode('/', $type)[0];
        if ($name == 'audio') {
            $name = 'voice';
        } elseif (in_array($name, $types)) {
            $name = 'file';
        }
        
        return MediaType::whereName($name)->first()->id;
        
    }
    
}
