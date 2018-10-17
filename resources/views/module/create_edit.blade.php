<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($module['id']))
                {{ Form::hidden('id', $module['id'], ['id' => 'id']) }}
            @endif
            <!-- 名称 -->
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
            <!-- 所属学校 -->
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'wap_site_id',
                'items' => $schools
            ])
            <!-- 控制器 -->
            @include('partials.single_select', [
                'label' => '控制器',
                'id' => 'tab_id',
                'items' => $tabs
            ])
            <!-- uri -->
            <div class="form-group">
                {!! Form::label('uri', 'uri', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('uri', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过40个汉字',
                        'required' => 'true',
                        'maxlength' => 255
                    ]) !!}
                </div>
            </div>
            @include('partials.remark')
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
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $module['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>