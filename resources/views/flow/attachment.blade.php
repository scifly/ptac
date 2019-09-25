<div class="form-group">
    @include('shared.label', ['field' => 'media_ids', 'label' => '附件'])
    <div class="col-sm-6">
        {!! Form::label(
            'attachment',
            Html::tag('i', ' 上传附件', ['class' => 'fa fa-cloud-upload']),
            ['class' => 'custom-file-upload text-blue'],
            false
        ) !!}
        {!! Form::file('attachment', [
            'multiple', 'id' => 'attachment',
            'accept' => '*', 'class' => 'file-upload'
        ]) !!}
        {!! Form::hidden('media_ids', null) !!}
    </div>
</div>