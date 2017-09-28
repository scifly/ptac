<?php
namespace App\Helpers;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ControllerTrait {

    /**
     * 文件上传公共方法
     *
     * @param UploadedFile $file
     * @param int $remark
     * @return array|bool
     */
    public function uploadedMedias(UploadedFile $file, $remark = 0) {

        if ($file->isValid()) {
            // 获取文件相关信息
            # 文件原名
            $originalName = $file->getClientOriginalName();
            # 扩展名
            $ext = $file->getClientOriginalExtension();
            # 临时文件的绝对路径
            $realPath = $file->getRealPath();
            # image/jpeg/
            $type = $this->getMediaType($file->getClientMimeType());
            // 上传文件
            $filename = uniqid() . '.' . $ext;
            // 使用新建的uploads本地存储空间（目录）
            if (Storage::disk('uploads')->put($filename, file_get_contents($realPath))) {
                $filePath = 'storage/app/uploads/' .
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

    private function getMediaType($type) {

        switch (explode('/', $type)[0]) {
            case 'image':
                return 1;
                break;
            case 'audio':
                return 2;
                break;
            case 'video':
                return 3;
                break;
            case 'application':
                return 4;
                break;
            default:
                return 5;
        }

    }

}

