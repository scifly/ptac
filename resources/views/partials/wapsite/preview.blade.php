<div class="form-group">
    {!! Form::label('media_ids', '轮播图', [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        <div class="preview">
            @if (isset($medias))
                @foreach($medias as $media)
                    <div class="img-item">
                        <img src="../../{{ $media->path }}" id="{{ $media->id }}">
                        {!! Form::hidden('media_ids[]', $media->id) !!}
                        <div class="del-mask">
                            <i class="delete fa fa-trash"></i>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <a id="upload" href="#" data-toggle="modal" data-target="#modalPic">
            <i class="fa fa-cloud-upload"></i> 上传图片
        </a>
    </div>
</div>