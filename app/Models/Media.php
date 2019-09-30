<?php
namespace App\Models;

use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Storage};
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
 * @property-read Collection|Menu[] $menus
 * @property-read int|null $menus_count
 * @property-read Collection|Article[] $articles
 * @property-read int|null $articles_count
 * @property-read MediaType $mType
 * @property-read Collection|Column[] $columns
 * @property-read int|null $columns_count
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
    
    protected $fillable = ['media_type_id', 'path', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function mType() { return $this->belongsTo('App\Models\MediaType', 'media_type_id'); }
    
    /** @return HasMany */
    function columns() { return $this->hasMany('App\Models\Column'); }
    
    /** @return HasMany */
    function articles() { return $this->hasMany('App\Models\Article'); }
    
    /** @return HasMany */
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
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除媒体
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'reset.media_id'  => ['Article', 'Column', 'Face', 'Flow', 'Menu', 'Module'],
            'clear.media_ids' => ['Article', 'Wap'],
        ]);
        
    }
    
    /**
     * 文件上传公共方法
     *
     * @param UploadedFile $file
     * @param string $remark
     * @return array|bool
     * @throws Throwable
     */
    function upload($file, $remark = '') {
        
        try {
            $ex = new Exception(__('messages.file_upload_failed'));
            throw_if(empty($file) || !$file->isValid(), $ex);
            $filename = uniqid() . '-' . $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $realPath = $file->getRealPath();
            $type = $this->mediaTypeId($file->getClientMimeType());
            $stored = Storage::disk('uploads')->put(
                date('Y/m/d/', time()) . $filename,
                file_get_contents($realPath)
            );
            throw_if(!$stored, $ex);
            $filePath = $this->filePath($filename);
            $media = $this->create([
                'path'          => $filePath,
                'remark'        => $remark,
                'media_type_id' => $type,
                'enabled'       => Constant::ENABLED,
            ]);
        } catch (Exception $e) {
            throw $e;
        }
        
        return [
            'id'       => $media->id,
            'path'     => $filePath,
            'type'     => $ext,
            'filename' => $filename,
        ];
        
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
