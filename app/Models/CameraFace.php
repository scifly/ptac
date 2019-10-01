<?php
namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class CameraFace
 *
 * @property int $id
 * @property int $camera_id
 * @property int $face_id
 * @property int|null $v_type
 * @property int|null $v_start
 * @property int|null $v_end
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Camera $camera
 * @property-read Face $face
 * @method static Builder|CameraFace newModelQuery()
 * @method static Builder|CameraFace newQuery()
 * @method static Builder|CameraFace query()
 * @method static Builder|CameraFace whereCameraId($value)
 * @method static Builder|CameraFace whereCreatedAt($value)
 * @method static Builder|CameraFace whereEnabled($value)
 * @method static Builder|CameraFace whereFaceId($value)
 * @method static Builder|CameraFace whereId($value)
 * @method static Builder|CameraFace whereUpdatedAt($value)
 * @method static Builder|CameraFace whereVEnd($value)
 * @method static Builder|CameraFace whereVStart($value)
 * @method static Builder|CameraFace whereVType($value)
 * @mixin Eloquent
 */
class CameraFace extends Pivot {

    protected $fillable = [
        'camera_id', 'face_id', 'v_type',
        'v_start', 'v_end', 'enabled'
    ];
    
    /** @return BelongsTo */
    function camera() { return $this->belongsTo('App\Models\Camera'); }
    
    /** @return BelongsTo */
    function face() { return $this->belongsTo('App\Models\Face'); }
    
    /**
     * 按人脸id保存绑定关系
     *
     * @param $faceId
     * @param array $cameraIds
     * @return bool
     * @throws Throwable
     */
    function storeByFaceId($faceId, array $cameraIds) {
        
        try {
            DB::transaction(function () use ($faceId, $cameraIds) {
                !in_array(0, $cameraIds) ?: $cameraIds = Camera::pluck('id');
                $records = [];
                foreach ($cameraIds as $cameraId) {
                    $this->whereFaceId($faceId)->delete();
                    $records[] = array_combine(
                        $this->fillable,
                        [$cameraId, $faceId, 1, 0, 0, 1]
                    );
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
