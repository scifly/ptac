<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($wsm['id']))
                {{ Form::hidden('id', $wsm['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
                'label' => '所属网站',
                'id' => 'wap_site_id',
                'items' => $wapSites
            ])
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过40个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 40]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('media_id', '模块图片', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="preview">
                        {!! Form::hidden('media_id', isset($media) ? $media->id : null, [
                            'id' => 'media_id'
                        ]) !!}
                        @if (isset($media))
                            <img src="../../{{ $media->path }}" id="{{ $media->id }}">
                        @endif
                    </div>
                    <label for="file-image" class="custom-file-upload text-blue">
                        <i class="fa fa-cloud-upload"></i> 上传图片
                    </label>
                    {!! Form::file('file-image', [
                        'id' => 'file-image',
                        'accept' => 'image/*',
                        'class' => 'file-upload',
                    ]) !!}
                </div>
            </div>
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $wsm['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>