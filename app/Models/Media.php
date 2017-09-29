<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Menu[] $menus
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mediaType() { return $this->belongsTo('App\Models\MediaType'); }
    
    public function wapSiteModule() { return $this->hasOne('App\Models\WapSiteModule'); }
    
    public function wsmArticle() { return $this->hasOne('App\Models\WsmArticle'); }
    
    /**
     * 获取指定媒体所包含的所有菜单对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus() { return $this->hasMany('App\Models\Menu'); }
    
    /**
     * 根据媒体ID返回媒体对象
     *
     * @param string $ids
     * @return array
     */
    public function medias($ids) {
        
        $ids = explode(',', $ids);
        $medias = [];
        foreach ($ids as $mediaId) {
            $medias[] = $this->find($mediaId);
        }
        
        return $medias;
        
    }
    
    /**
     * 保存媒体
     *
     * @param array $data
     * @return bool
     */
    public function store(array $data) {
        
        $media = $this->create($data);
        
        return $media ? true : false;
        
    }
    
    /**
     * 更新媒体
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    public function modify(array $data, $id) {
        
        $media = $this->find($id);
        if (!$media) {
            return false;
        }
        
        return $media->update($data) ? true : false;
        
    }
    
    /**
     * 删除删除媒体
     *
     * @param $id
     * @return bool|null
     */
    public function remove($id) {
        
        $media = $this->find($id);
        if (!$media) { return false; }
        return $media->removable($media) ? $media->delete() : false;
        
    }
    
}
