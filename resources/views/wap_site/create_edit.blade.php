<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            {{ Form::hidden('id', $ws['id'], ['id' => 'id']) }}
            <div class="form-group">
                {!! Form::label('site_title', '首页抬头', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-3">
                    {!! Form::text('site_title', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过40个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 40]'
                    ]) !!}
                </div>
            </div>
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
                                    <input type="hidden" name="media_ids[]" value="{{ $media->id }}"/>
                                    <div class="del-mask">
                                        <i class="delete fa fa-trash"></i>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $ws['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
<div class="modal fade" id="modalPic">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">模态框（Modal）标题</h4>
            </div>
            <div class="modal-body">
                <input type="file" name="img[]" id="uploadFile" accept="image/jpeg,image/gif,image/png" multiple>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
