<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($col))
                {!! Form::hidden('id', $col['id']) !!}
            @endif
            @include('shared.single_select', [
                'label' => '所属网站',
                'id' => 'wap_id',
                'items' => $waps
            ])
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
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
                @include('shared.label', ['field' => 'media_id', 'label' => '模块图片'])
                <div class="col-sm-6">
                    <div class="preview">
                        {!! Form::hidden('media_id', isset($media) ? $media->id : null) !!}
                        @if (isset($media))
                            {!! Html::image('../../' . $media->path, null, [
                                'id' => $media->id
                            ]) !!}
                        @endif
                    </div>
                    {!! Form::label(
                        'file-image', Html::tag('i', ' 上传图片', ['class' => 'fa fa-cloud-upload']),
                        ['class' => 'custom-file-upload text-blue'], false
                    ) !!}
                    {!! Form::file('file-image', [
                        'accept' => 'image/*', 'class' => 'file-upload',
                    ]) !!}
                </div>
            </div>
            @include('shared.switch', ['value' => $col['enabled'] ?? null])
        </div>
    </div>
    @include('shared.form_buttons')
</div>